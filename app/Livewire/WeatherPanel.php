<?php

namespace App\Livewire;

use App\Services\AirQualityService;
use App\Services\ForecastService;
use App\Services\WeatherService;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Attributes\On;
use Livewire\Component;

class WeatherPanel extends Component
{
    private const MAX_FETCHES_PER_MINUTE = 30;

    public bool $isOpen = false;

    public ?int $locationId = null;

    public string $locationName = '';

    public ?float $lat = null;

    public ?float $lng = null;

    public bool $loading = false;

    public ?string $error = null;

    /** @var array<string, mixed>|null */
    public ?array $weather = null;

    /** @var array<string, mixed>|null */
    public ?array $forecast = null;

    /** @var array<string, mixed>|null */
    public ?array $airQuality = null;

    #[On('locationChosen')]
    public function open(
        int $id,
        string $name,
        float $lat,
        float $lng,
        WeatherService $weatherService,
        ForecastService $forecastService,
        AirQualityService $airQualityService,
    ): void {
        $this->isOpen = true;
        $this->locationId = $id;
        $this->locationName = $name;
        $this->lat = $lat;
        $this->lng = $lng;
        $this->error = null;
        $this->weather = null;
        $this->forecast = null;
        $this->airQuality = null;
        $this->loading = true;

        $rateLimitKey = 'weather-fetch:'.session()->getId();

        if (RateLimiter::tooManyAttempts($rateLimitKey, self::MAX_FETCHES_PER_MINUTE)) {
            $this->error = 'Too many requests — please wait a moment and try again.';
            $this->loading = false;

            return;
        }

        RateLimiter::hit($rateLimitKey);

        $this->weather = $this->fetch(fn () => $weatherService->current($lat, $lng)->toArray(), $id, $lat, $lng);
        $this->forecast = $this->fetch(fn () => $forecastService->forecast($lat, $lng)->toArray(), $id, $lat, $lng);
        $this->airQuality = $this->fetch(fn () => $airQualityService->current($lat, $lng)->toArray(), $id, $lat, $lng);

        if (! $this->weather && ! $this->forecast && ! $this->airQuality) {
            $this->error = 'Unable to load weather data right now. Please try again.';
        }

        $this->loading = false;
    }

    /**
     * Runs one API call in isolation, so a failure here doesn't stop the other two being attempted.
     *
     * @param  Closure(): array<string, mixed>  $callback
     * @return array<string, mixed>|null
     */
    private function fetch(Closure $callback, int $locationId, float $lat, float $lng): ?array
    {
        try {
            return $callback();
        } catch (ConnectionException|RequestException $exception) {
            Log::error('Failed to load weather data for location.', [
                'location_id' => $locationId,
                'lat' => $lat,
                'lng' => $lng,
                'exception' => $exception->getMessage(),
            ]);

            return null;
        }
    }

    #[On('locationDeleted')]
    public function closeIfShowingDeletedLocation(int $id): void
    {
        if ($this->locationId === $id) {
            $this->close();
        }
    }

    #[On('locationSelected')]
    public function closeOnNewLocationClick(): void
    {
        $this->close();
    }

    public function close(): void
    {
        $this->isOpen = false;
    }

    public function render(): View
    {
        return view('livewire.weather-panel');
    }
}
