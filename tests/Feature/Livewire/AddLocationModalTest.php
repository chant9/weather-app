<?php

use App\Livewire\AddLocationModal;
use App\Models\Location;
use Livewire\Livewire;

it('opens with the selected coordinates when locationSelected is dispatched', function () {
    Livewire::test(AddLocationModal::class)
        ->dispatch('locationSelected', lat: 51.5, lng: -0.1)
        ->assertSet('isOpen', true)
        ->assertSet('lat', 51.5)
        ->assertSet('lng', -0.1);
});

it('requires a name before saving', function () {
    Livewire::test(AddLocationModal::class)
        ->dispatch('locationSelected', lat: 51.5, lng: -0.1)
        ->set('name', '')
        ->call('save')
        ->assertHasErrors(['name' => 'required']);

    expect(Location::count())->toBe(0);
});

it('saves the location, dispatches locationSaved, and closes', function () {
    Livewire::test(AddLocationModal::class)
        ->dispatch('locationSelected', lat: 51.5, lng: -0.1)
        ->set('name', 'Home')
        ->call('save')
        ->assertDispatched('locationSaved')
        ->assertSet('isOpen', false);

    expect(Location::where('name', 'Home')->exists())->toBeTrue();
});

it('rejects saving a location with a name that already exists, case-insensitively', function () {
    Location::factory()->create(['name' => 'Home']);

    Livewire::test(AddLocationModal::class)
        ->dispatch('locationSelected', lat: 51.5, lng: -0.1)
        ->set('name', 'home')
        ->call('save')
        ->assertHasErrors('name')
        ->assertNotDispatched('locationSaved')
        ->assertSet('isOpen', true);

    expect(Location::count())->toBe(1);
});

it('rejects saving a location that already exists at the same coordinates', function () {
    Location::factory()->create(['lat' => 51.5, 'lng' => -0.1]);

    Livewire::test(AddLocationModal::class)
        ->dispatch('locationSelected', lat: 51.5, lng: -0.1)
        ->set('name', 'Duplicate')
        ->call('save')
        ->assertHasErrors('name')
        ->assertNotDispatched('locationSaved')
        ->assertSet('isOpen', true);

    expect(Location::count())->toBe(1);
});
