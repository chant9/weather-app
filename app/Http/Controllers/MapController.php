<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Contracts\View\View;

class MapController extends Controller
{
    public function index(): View
    {
        return view('map', [
            'hasLocations' => Location::query()->exists(),
        ]);
    }
}
