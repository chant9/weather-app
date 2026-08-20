<?php

use App\Livewire\LocationList;
use App\Models\Location;
use Livewire\Livewire;

it('lists saved locations', function () {
    Location::factory()->create(['name' => 'Home']);
    Location::factory()->create(['name' => 'Office']);

    Livewire::test(LocationList::class)
        ->assertSee('Home')
        ->assertSee('Office');
});

it('lists locations alphabetically regardless of creation order', function () {
    Location::factory()->create(['name' => 'Zurich']);
    Location::factory()->create(['name' => 'Amsterdam']);
    Location::factory()->create(['name' => 'Manchester']);

    $names = Livewire::test(LocationList::class)
        ->viewData('locations')
        ->pluck('name')
        ->all();

    expect($names)->toBe(['Amsterdam', 'Manchester', 'Zurich']);
});

it('shows an empty state when there are no locations', function () {
    Livewire::test(LocationList::class)
        ->assertSee('Click the map to save your first location.');
});

it('starts collapsed when there are no saved locations', function () {
    Livewire::test(LocationList::class)
        ->assertSeeHtml('collapsed: true');
});

it('starts expanded when there are saved locations', function () {
    Location::factory()->create();

    Livewire::test(LocationList::class)
        ->assertSeeHtml('collapsed: false');
});

it('dispatches locationChosen and centerMapOnLocation when a location is chosen', function () {
    $location = Location::factory()->create(['name' => 'Home', 'lat' => 51.5, 'lng' => -0.1]);

    Livewire::test(LocationList::class)
        ->call('choose', $location->id)
        ->assertDispatched('locationChosen', id: $location->id, name: 'Home', lat: 51.5, lng: -0.1)
        ->assertDispatched('centerMapOnLocation', lat: 51.5, lng: -0.1);
});

it('deletes a location and dispatches locationDeleted with its name and the remaining count', function () {
    $location = Location::factory()->create(['name' => 'Home']);
    Location::factory()->create();

    Livewire::test(LocationList::class)
        ->call('delete', $location->id)
        ->assertDispatched('locationDeleted', id: $location->id, name: 'Home', remaining: 1);

    expect(Location::find($location->id))->toBeNull();
});

it('reports zero remaining when the last location is deleted', function () {
    $location = Location::factory()->create();

    Livewire::test(LocationList::class)
        ->call('delete', $location->id)
        ->assertDispatched('locationDeleted', id: $location->id, remaining: 0);
});
