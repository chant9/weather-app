<?php

use App\Models\Location;

it('renders the full-screen map page with the core livewire components', function () {
    $response = $this->get('/');

    $response->assertOk();
    $response->assertSeeLivewire('map-interaction');
    $response->assertSeeLivewire('location-list');
    $response->assertSeeLivewire('add-location-modal');
    $response->assertSeeLivewire('weather-panel');
});

it('shows the onboarding overlay when there are no saved locations', function () {
    $html = $this->get('/')->getContent();

    expect($html)->toContain('z-40 flex items-center justify-center bg-black/50');
});

it('hides the onboarding overlay when at least one location is saved', function () {
    Location::factory()->create();

    $html = $this->get('/')->getContent();

    expect($html)->toContain('z-40 hidden items-center justify-center bg-black/50');
});
