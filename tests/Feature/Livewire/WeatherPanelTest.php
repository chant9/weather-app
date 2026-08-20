<?php

use App\Livewire\WeatherPanel;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;

it('loads weather, forecast, and air quality when a location is chosen', function () {
    Http::fake([
        '*/data/2.5/weather*' => Http::response([
            'weather' => [['main' => 'Clear', 'description' => 'clear sky', 'icon' => '01d']],
            'main' => ['temp' => 18.0, 'feels_like' => 17.5, 'pressure' => 1013, 'humidity' => 55],
            'wind' => ['speed' => 3.0, 'deg' => 180],
            'dt' => now()->timestamp,
        ], 200),
        '*/data/2.5/forecast*' => Http::response(['list' => []], 200),
        '*/data/2.5/air_pollution*' => Http::response([
            'list' => [['main' => ['aqi' => 1], 'components' => ['co' => 100.0]]],
        ], 200),
    ]);

    Livewire::test(WeatherPanel::class)
        ->dispatch('locationChosen', id: 1, name: 'Home', lat: 51.5, lng: -0.1)
        ->assertSet('isOpen', true)
        ->assertSet('loading', false)
        ->assertSet('error', null)
        ->assertSet('weather.temperature', 18.0)
        ->assertSet('airQuality.label', 'Good');
});

it('closes when the currently shown location is deleted', function () {
    Http::fake([
        '*/data/2.5/weather*' => Http::response([
            'weather' => [['main' => 'Clear', 'description' => 'clear sky', 'icon' => '01d']],
            'main' => ['temp' => 18.0, 'feels_like' => 17.5, 'pressure' => 1013, 'humidity' => 55],
            'wind' => ['speed' => 3.0, 'deg' => 180],
            'dt' => now()->timestamp,
        ], 200),
        '*/data/2.5/forecast*' => Http::response(['list' => []], 200),
        '*/data/2.5/air_pollution*' => Http::response([
            'list' => [['main' => ['aqi' => 1], 'components' => ['co' => 100.0]]],
        ], 200),
    ]);

    Livewire::test(WeatherPanel::class)
        ->dispatch('locationChosen', id: 5, name: 'Home', lat: 51.5, lng: -0.1)
        ->assertSet('isOpen', true)
        ->dispatch('locationDeleted', id: 5, remaining: 0)
        ->assertSet('isOpen', false);
});

it('stays open when a different location is deleted', function () {
    Http::fake([
        '*/data/2.5/weather*' => Http::response([
            'weather' => [['main' => 'Clear', 'description' => 'clear sky', 'icon' => '01d']],
            'main' => ['temp' => 18.0, 'feels_like' => 17.5, 'pressure' => 1013, 'humidity' => 55],
            'wind' => ['speed' => 3.0, 'deg' => 180],
            'dt' => now()->timestamp,
        ], 200),
        '*/data/2.5/forecast*' => Http::response(['list' => []], 200),
        '*/data/2.5/air_pollution*' => Http::response([
            'list' => [['main' => ['aqi' => 1], 'components' => ['co' => 100.0]]],
        ], 200),
    ]);

    Livewire::test(WeatherPanel::class)
        ->dispatch('locationChosen', id: 5, name: 'Home', lat: 51.5, lng: -0.1)
        ->assertSet('isOpen', true)
        ->dispatch('locationDeleted', id: 99, remaining: 1)
        ->assertSet('isOpen', true);
});

it('closes when the map is clicked to add a new location', function () {
    Http::fake([
        '*/data/2.5/weather*' => Http::response([
            'weather' => [['main' => 'Clear', 'description' => 'clear sky', 'icon' => '01d']],
            'main' => ['temp' => 18.0, 'feels_like' => 17.5, 'pressure' => 1013, 'humidity' => 55],
            'wind' => ['speed' => 3.0, 'deg' => 180],
            'dt' => now()->timestamp,
        ], 200),
        '*/data/2.5/forecast*' => Http::response(['list' => []], 200),
        '*/data/2.5/air_pollution*' => Http::response([
            'list' => [['main' => ['aqi' => 1], 'components' => ['co' => 100.0]]],
        ], 200),
    ]);

    Livewire::test(WeatherPanel::class)
        ->dispatch('locationChosen', id: 5, name: 'Home', lat: 51.5, lng: -0.1)
        ->assertSet('isOpen', true)
        ->dispatch('locationSelected', lat: 10.0, lng: 20.0)
        ->assertSet('isOpen', false);
});

it('sets an error message when the weather api call fails', function () {
    config(['weather.retry_times' => 1, 'weather.retry_sleep_ms' => 1]);

    Http::fake([
        'api.openweathermap.org/*' => Http::response([], 500),
    ]);

    Livewire::test(WeatherPanel::class)
        ->dispatch('locationChosen', id: 1, name: 'Home', lat: 51.5, lng: -0.1)
        ->assertSet('loading', false)
        ->assertSet('error', 'Unable to load weather data right now. Please try again.')
        ->assertSet('weather', null);
});

it('still shows weather and air quality when only the forecast call fails', function () {
    config(['weather.retry_times' => 1, 'weather.retry_sleep_ms' => 1]);

    Http::fake([
        '*/data/2.5/weather*' => Http::response([
            'weather' => [['main' => 'Clear', 'description' => 'clear sky', 'icon' => '01d']],
            'main' => ['temp' => 18.0, 'feels_like' => 17.5, 'pressure' => 1013, 'humidity' => 55],
            'wind' => ['speed' => 3.0, 'deg' => 180],
            'dt' => now()->timestamp,
        ], 200),
        '*/data/2.5/forecast*' => Http::response([], 500),
        '*/data/2.5/air_pollution*' => Http::response([
            'list' => [['main' => ['aqi' => 1], 'components' => ['co' => 100.0]]],
        ], 200),
    ]);

    Livewire::test(WeatherPanel::class)
        ->dispatch('locationChosen', id: 1, name: 'Home', lat: 51.5, lng: -0.1)
        ->assertSet('error', null)
        ->assertSet('weather.temperature', 18.0)
        ->assertSet('forecast', null)
        ->assertSet('airQuality.label', 'Good');
});

it('rate limits repeated weather fetches within a minute', function () {
    Http::fake([
        '*/data/2.5/weather*' => Http::response([
            'weather' => [['main' => 'Clear', 'description' => 'clear sky', 'icon' => '01d']],
            'main' => ['temp' => 18.0, 'feels_like' => 17.5, 'pressure' => 1013, 'humidity' => 55],
            'wind' => ['speed' => 3.0, 'deg' => 180],
            'dt' => now()->timestamp,
        ], 200),
        '*/data/2.5/forecast*' => Http::response(['list' => []], 200),
        '*/data/2.5/air_pollution*' => Http::response([
            'list' => [['main' => ['aqi' => 1], 'components' => ['co' => 100.0]]],
        ], 200),
    ]);

    $component = Livewire::test(WeatherPanel::class);

    for ($i = 0; $i < 30; $i++) {
        $component->dispatch('locationChosen', id: 1, name: 'Home', lat: 51.5, lng: -0.1);
    }

    $component
        ->dispatch('locationChosen', id: 1, name: 'Home', lat: 51.5, lng: -0.1)
        ->assertSet('error', 'Too many requests — please wait a moment and try again.');
});
