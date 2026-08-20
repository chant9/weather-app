<?php

namespace App\DataTransferObjects;

use Illuminate\Support\Carbon;

final readonly class ForecastHour
{
    public function __construct(
        public Carbon $dateTime,
        public float $temperature,
        public string $condition,
        public string $description,
        public string $icon,
    ) {}

    /**
     * @param  array<string, mixed>  $entry
     */
    public static function fromApiEntry(array $entry): self
    {
        $weather = $entry['weather'][0] ?? [];

        return new self(
            dateTime: Carbon::createFromTimestamp($entry['dt']),
            temperature: (float) ($entry['main']['temp'] ?? 0),
            condition: $weather['main'] ?? '',
            description: $weather['description'] ?? '',
            icon: $weather['icon'] ?? '01d',
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'dateTime' => $this->dateTime->toIso8601String(),
            'temperature' => $this->temperature,
            'condition' => $this->condition,
            'description' => $this->description,
            'icon' => $this->icon,
        ];
    }
}
