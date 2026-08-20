<?php

namespace App\Services;

use App\DataTransferObjects\WeatherData;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class WeatherService
{
    public function current(float $lat, float $lng): WeatherData
    {
        $data = Cache::remember(
            $this->cacheKey($lat, $lng),
            config('weather.cache_ttl'),
            fn () => $this->fetch($lat, $lng),
        );

        return WeatherData::fromApiResponse($data);
    }

    /**
     * @return array<string, mixed>
     */
    protected function fetch(float $lat, float $lng): array
    {
        $response = Http::retry(config('weather.retry_times'), config('weather.retry_sleep_ms'))
            ->get(config('weather.base_url').'/data/2.5/weather', [
                'lat' => $lat,
                'lon' => $lng,
                'appid' => config('weather.api_key'),
                'units' => config('weather.units'),
            ])
            ->throw();

        return $response->json();
    }

    protected function cacheKey(float $lat, float $lng): string
    {
        return sprintf('weather_current_%s_%s', round($lat, 4), round($lng, 4));
    }
}
