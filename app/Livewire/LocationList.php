<?php

namespace App\Livewire;

use App\Models\Location;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

class LocationList extends Component
{
    #[On('locationSaved')]
    #[On('locationDeleted')]
    public function refresh(): void
    {
        // Empty — just forces a re-render; render() re-queries locations itself.
    }

    public function choose(int $id): void
    {
        $location = Location::findOrFail($id);

        $this->dispatch(
            'locationChosen',
            id: $location->id,
            name: $location->name,
            lat: (float) $location->lat,
            lng: (float) $location->lng,
        );

        $this->dispatch(
            'centerMapOnLocation',
            lat: (float) $location->lat,
            lng: (float) $location->lng,
        );
    }

    public function delete(int $id): void
    {
        $location = Location::findOrFail($id);
        $name = $location->name;
        $location->delete();

        $this->dispatch('locationDeleted', id: $id, name: $name, remaining: Location::count());
    }

    public function render(): View
    {
        return view('livewire.location-list', [
            'locations' => Location::query()->orderBy('name')->get(),
        ]);
    }
}
