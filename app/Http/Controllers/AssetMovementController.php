<?php

namespace App\Http\Controllers;

use App\Models\AssetMovement;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AssetMovementController extends Controller
{
    public function index(): View
    {
        $movements = AssetMovement::with(['asset', 'fromLocation', 'toLocation', 'toUser', 'creator'])->latest('moved_at')->paginate(15);
        return view('movements.index', compact('movements'));
    }
}
