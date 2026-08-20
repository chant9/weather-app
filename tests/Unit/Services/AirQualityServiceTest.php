<?php

use App\Services\AirQualityService;
use Illuminate\Support\Facades\Http;

it('fetches and maps air quality data from the openweathermap api', function () {
    Http::fake([
        'api.openweathermap.org/data/2.5/air_pollution*' => Http::response([
            'list' => [[
                'main' => ['aqi' => 2],
                'components' => ['co' => 200.1, 'no2' => 10.5, 'o3' => 68.3, 'pm2_5' => 8.2, 'pm10' => 12.1],
            ]],
        ], 200),
    ]);

    $airQuality = app(AirQualityService::class)->current(51.5, -0.1);

    expect($airQuality->aqi)->toBe(2)
        ->and($airQuality->label)->toBe('Fair')
        ->and($airQuality->components['co'])->toBe(200.1);

    Http::assertSentCount(1);
});
