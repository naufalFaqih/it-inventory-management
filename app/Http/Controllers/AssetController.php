<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AssetController extends Controller
{
    public function index(): View
    {
        $assets = Asset::with(['category', 'location', 'assignedUser'])->latest()->paginate(15);
        return view('assets.index', compact('assets'));
    }
}
