<?php

namespace App\Services;

use App\DataTransferObjects\AirQualityData;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class AirQualityService
{
    public function current(float $lat, float $lng): AirQualityData
    {
        $data = Cache::remember(
            $this->cacheKey($lat, $lng),
            config('weather.cache_ttl'),
            fn () => $this->fetch($lat, $lng),
        );

        return AirQualityData::fromApiResponse($data);
    }

    /**
     * @return array<string, mixed>
     */
    protected function fetch(float $lat, float $lng): array
    {
        $response = Http::retry(config('weather.retry_times'), config('weather.retry_sleep_ms'))
            ->get(config('weather.base_url').'/data/2.5/air_pollution', [
                'lat' => $lat,
                'lon' => $lng,
                'appid' => config('weather.api_key'),
            ])
            ->throw();

        return $response->json();
    }

    protected function cacheKey(float $lat, float $lng): string
    {
        return sprintf('weather_air_quality_%s_%s', round($lat, 4), round($lng, 4));
    }
}
