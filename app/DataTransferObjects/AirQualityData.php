<?php

namespace App\DataTransferObjects;

final readonly class AirQualityData
{
    /**
     * @param  array<string, float>  $components
     */
    public function __construct(
        public int $aqi,
        public string $label,
        public array $components,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromApiResponse(array $data): self
    {
        $item = $data['list'][0] ?? [];
        $aqi = (int) ($item['main']['aqi'] ?? 1);

        return new self(
            aqi: $aqi,
            label: self::labelFor($aqi),
            components: array_map(
                fn ($value) => (float) $value,
                $item['components'] ?? [],
            ),
        );
    }

    private static function labelFor(int $aqi): string
    {
        return match ($aqi) {
            1 => 'Good',
            2 => 'Fair',
            3 => 'Moderate',
            4 => 'Poor',
            5 => 'Very Poor',
            default => 'Unknown',
        };
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'aqi' => $this->aqi,
            'label' => $this->label,
            'components' => $this->components,
        ];
    }
}
