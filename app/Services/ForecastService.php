<?php

namespace App\Services;

use App\DataTransferObjects\ForecastData;
use App\DataTransferObjects\ForecastDay;
use App\DataTransferObjects\ForecastHour;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class ForecastService
{
    public function forecast(float $lat, float $lng): ForecastData
    {
        $entries = Cache::remember(
            $this->cacheKey($lat, $lng),
            config('weather.cache_ttl'),
            fn () => $this->fetch($lat, $lng),
        );

        return new ForecastData(
            hourly: $this->buildHourly($entries),
            daily: $this->buildDaily($entries),
        );
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    protected function fetch(float $lat, float $lng): array
    {
        $response = Http::retry(config('weather.retry_times'), config('weather.retry_sleep_ms'))
            ->get(config('weather.base_url').'/data/2.5/forecast', [
                'lat' => $lat,
                'lon' => $lng,
                'appid' => config('weather.api_key'),
                'units' => config('weather.units'),
            ])
            ->throw();

        /** @var array<int, array<string, mixed>> $entries */
        $entries = $response->json('list', []);

        return $entries;
    }

    /**
     * @param  array<int, array<string, mixed>>  $entries
     * @return array<int, ForecastHour>
     */
    protected function buildHourly(array $entries): array
    {
        return array_map(
            fn (array $entry) => ForecastHour::fromApiEntry($entry),
            array_slice($entries, 0, 8),
        );
    }

    /**
     * @param  array<int, array<string, mixed>>  $entries
     * @return array<int, ForecastDay>
     */
    protected function buildDaily(array $entries): array
    {
        $byDate = collect($entries)->groupBy(
            fn (array $entry) => Carbon::createFromTimestamp($entry['dt'])->toDateString(),
        );

        return $byDate
            ->map(fn ($dayEntries, string $date) => $this->summariseDay($date, $dayEntries->all()))
            ->values()
            ->all();
    }

    /**
     * @param  array<int, array<string, mixed>>  $dayEntries
     */
    protected function summariseDay(string $date, array $dayEntries): ForecastDay
    {
        $temps = collect($dayEntries)->map(fn (array $entry) => (float) ($entry['main']['temp'] ?? 0));

        $midday = collect($dayEntries)
            ->sortBy(fn (array $entry) => abs(Carbon::createFromTimestamp($entry['dt'])->hour - 12))
            ->first();

        $weather = $midday['weather'][0] ?? [];

        return new ForecastDay(
            date: Carbon::parse($date),
            minTemperature: (float) $temps->min(),
            maxTemperature: (float) $temps->max(),
            condition: $weather['main'] ?? '',
            description: $weather['description'] ?? '',
            icon: $weather['icon'] ?? '01d',
        );
    }

    protected function cacheKey(float $lat, float $lng): string
    {
        return sprintf('weather_forecast_%s_%s', round($lat, 4), round($lng, 4));
    }
}
