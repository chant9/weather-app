<?php

namespace App\Livewire;

use App\Models\Location;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class MapInteraction extends Component
{
    /**
     * @var array<int, array{id: int, name: string, lat: float, lng: float}>
     */
    public array $locations = [];

    public function mount(): void
    {
        $this->locations = Location::query()
            ->get(['id', 'name', 'lat', 'lng'])
            ->map(fn (Location $location) => [
                'id' => $location->id,
                'name' => $location->name,
                'lat' => (float) $location->lat,
                'lng' => (float) $location->lng,
            ])
            ->all();
    }

    public function selectLocation(float $lat, float $lng): void
    {
        $this->dispatch('locationSelected', lat: $lat, lng: $lng);
    }

    public function render(): View
    {
        return view('livewire.map-interaction');
    }
}
