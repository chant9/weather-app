<?php

namespace App\Livewire;

use App\Models\Location;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Component;

class AddLocationModal extends Component
{
    public bool $isOpen = false;

    public ?float $lat = null;

    public ?float $lng = null;

    #[Validate('required|string|max:255')]
    public string $name = '';

    #[On('locationSelected')]
    public function open(float $lat, float $lng): void
    {
        $this->reset('name');
        $this->resetValidation();
        $this->lat = $lat;
        $this->lng = $lng;
        $this->isOpen = true;
    }

    public function save(): void
    {
        $this->validate();

        if (Location::whereRaw('LOWER(name) = ?', [Str::lower($this->name)])->exists()) {
            $this->addError('name', 'A location with this name already exists.');

            return;
        }

        if (Location::where('lat', $this->lat)->where('lng', $this->lng)->exists()) {
            $this->addError('name', 'This location has already been saved.');

            return;
        }

        $location = Location::create([
            'name' => $this->name,
            'lat' => $this->lat,
            'lng' => $this->lng,
        ]);

        $this->dispatch(
            'locationSaved',
            id: $location->id,
            name: $location->name,
            lat: (float) $location->lat,
            lng: (float) $location->lng,
        );

        $this->isOpen = false;
    }

    public function close(): void
    {
        $this->isOpen = false;
    }

    public function render(): View
    {
        return view('livewire.add-location-modal');
    }
}
