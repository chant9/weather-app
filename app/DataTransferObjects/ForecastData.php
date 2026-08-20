<?php

namespace App\DataTransferObjects;

final readonly class ForecastData
{
    /**
     * @param  array<int, ForecastHour>  $hourly
     * @param  array<int, ForecastDay>  $daily
     */
    public function __construct(
        public array $hourly,
        public array $daily,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'hourly' => array_map(fn (ForecastHour $hour) => $hour->toArray(), $this->hourly),
            'daily' => array_map(fn (ForecastDay $day) => $day->toArray(), $this->daily),
        ];
    }
}
