<?php

namespace App\DataTransferObjects;

use Illuminate\Support\Carbon;

final readonly class ForecastDay
{
    public function __construct(
        public Carbon $date,
        public float $minTemperature,
        public float $maxTemperature,
        public string $condition,
        public string $description,
        public string $icon,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'date' => $this->date->toDateString(),
            'minTemperature' => $this->minTemperature,
            'maxTemperature' => $this->maxTemperature,
            'condition' => $this->condition,
            'description' => $this->description,
            'icon' => $this->icon,
        ];
    }
}
