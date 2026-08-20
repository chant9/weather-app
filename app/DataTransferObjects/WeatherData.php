<?php

namespace App\DataTransferObjects;

use Illuminate\Support\Carbon;

final readonly class WeatherData
{
    public function __construct(
        public float $temperature,
        public float $feelsLike,
        public int $humidity,
        public int $pressure,
        public float $windSpeed,
        public int $windDirection,
        public string $condition,
        public string $description,
        public string $icon,
        public Carbon $observedAt,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromApiResponse(array $data): self
    {
        $weather = $data['weather'][0] ?? [];

        return new self(
            temperature: (float) ($data['main']['temp'] ?? 0),
            feelsLike: (float) ($data['main']['feels_like'] ?? 0),
            humidity: (int) ($data['main']['humidity'] ?? 0),
            pressure: (int) ($data['main']['pressure'] ?? 0),
            windSpeed: (float) ($data['wind']['speed'] ?? 0),
            windDirection: (int) ($data['wind']['deg'] ?? 0),
            condition: $weather['main'] ?? '',
            description: $weather['description'] ?? '',
            icon: $weather['icon'] ?? '01d',
            observedAt: Carbon::createFromTimestamp($data['dt'] ?? time()),
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'temperature' => $this->temperature,
            'feelsLike' => $this->feelsLike,
            'humidity' => $this->humidity,
            'pressure' => $this->pressure,
            'windSpeed' => $this->windSpeed,
            'windDirection' => $this->windDirection,
            'condition' => $this->condition,
            'description' => $this->description,
            'icon' => $this->icon,
            'observedAt' => $this->observedAt->toIso8601String(),
        ];
    }
}
