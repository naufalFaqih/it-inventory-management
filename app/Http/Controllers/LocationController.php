<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LocationController extends Controller
{
    public function index(): View
    {
        $locations = Location::with(['parent'])->withCount('assets')->latest()->paginate(15);
        return view('locations.index', compact('locations'));
    }
}
