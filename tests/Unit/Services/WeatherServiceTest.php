<?php

use App\Services\WeatherService;
use Illuminate\Support\Facades\Http;

it('fetches and maps current weather from the openweathermap api', function () {
    Http::fake([
        'api.openweathermap.org/data/2.5/weather*' => Http::response([
            'weather' => [['main' => 'Rain', 'description' => 'light rain', 'icon' => '10d']],
            'main' => ['temp' => 15.5, 'feels_like' => 15.0, 'pressure' => 1012, 'humidity' => 70],
            'wind' => ['speed' => 4.1, 'deg' => 250],
            'dt' => 1700000000,
        ], 200),
    ]);

    $weather = app(WeatherService::class)->current(51.5074, -0.1278);

    expect($weather->temperature)->toBe(15.5)
        ->and($weather->feelsLike)->toBe(15.0)
        ->and($weather->humidity)->toBe(70)
        ->and($weather->pressure)->toBe(1012)
        ->and($weather->windSpeed)->toBe(4.1)
        ->and($weather->windDirection)->toBe(250)
        ->and($weather->condition)->toBe('Rain')
        ->and($weather->description)->toBe('light rain')
        ->and($weather->icon)->toBe('10d');

    Http::assertSentCount(1);
});

it('caches the response for repeated calls to the same coordinates', function () {
    Http::fake([
        'api.openweathermap.org/*' => Http::response([
            'weather' => [['main' => 'Clear', 'description' => 'clear sky', 'icon' => '01d']],
            'main' => ['temp' => 20.0, 'feels_like' => 19.5, 'pressure' => 1015, 'humidity' => 50],
            'wind' => ['speed' => 2.0, 'deg' => 100],
            'dt' => 1700000000,
        ], 200),
    ]);

    $service = app(WeatherService::class);
    $service->current(51.5074, -0.1278);
    $service->current(51.5074, -0.1278);

    Http::assertSentCount(1);
});
