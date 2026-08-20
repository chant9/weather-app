<?php

use App\Services\ForecastService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;

it('buckets the 3-hourly forecast into an hourly window and daily summaries', function () {
    $start = Carbon::parse('2026-01-01 00:00:00', 'UTC');

    $entries = collect(range(0, 15))->map(function (int $i) use ($start) {
        $time = $start->copy()->addHours($i * 3);

        return [
            'dt' => $time->timestamp,
            'main' => ['temp' => 10 + $i],
            'weather' => [['main' => 'Clouds', 'description' => 'overcast clouds', 'icon' => '04d']],
        ];
    })->all();

    Http::fake([
        'api.openweathermap.org/data/2.5/forecast*' => Http::response(['list' => $entries], 200),
    ]);

    $forecast = app(ForecastService::class)->forecast(51.5, -0.1);

    expect($forecast->hourly)->toHaveCount(8)
        ->and($forecast->hourly[0]->temperature)->toBe(10.0)
        ->and($forecast->daily)->toHaveCount(2);

    $firstDay = $forecast->daily[0];

    expect($firstDay->minTemperature)->toBe(10.0)
        ->and($firstDay->maxTemperature)->toBe(17.0)
        ->and($firstDay->icon)->toBe('04d');

    Http::assertSentCount(1);
});
