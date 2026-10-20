<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMovementRequest;
use App\Models\Asset;
use App\Models\AssetMovement;
use App\Models\Location;
use App\Models\User;
use App\Services\MovementService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AssetMovementController extends Controller
{
    public function __construct(
        protected MovementService $movementService
    ) {}

    public function index(Request $request): View
    {
        $query = AssetMovement::with(['asset', 'fromLocation', 'toLocation', 'fromUser', 'toUser', 'creator']);

        if ($request->filled('asset_id')) {
            $query->where('asset_id', $request->input('asset_id'));
        }

        if ($request->filled('location_id')) {
            $locId = $request->input('location_id');
            $query->where(function ($q) use ($locId) {
                $q->where('from_location_id', $locId)
                    ->orWhere('to_location_id', $locId);
            });
        }

        $movements = $query->latest('moved_at')->paginate(15)->withQueryString();
        $locations = Location::orderBy('name')->get();

        return view('movements.index', compact('movements', 'locations'));
    }

    public function create(Request $request): View
    {
        $selectedAsset = $request->filled('asset_id') ? Asset::find($request->input('asset_id')) : null;
        $assets = Asset::where('status', '!=', Asset::STATUS_RETIRED)->orderBy('name')->get();
        $locations = Location::orderBy('name')->get();
        $users = User::where('is_active', true)->orderBy('name')->get();

        return view('movements.create', compact('selectedAsset', 'assets', 'locations', 'users'));
    }

    public function store(StoreMovementRequest $request): RedirectResponse
    {
        $asset = Asset::findOrFail($request->input('asset_id'));

        $this->movementService->recordMovement(
            $asset,
            $request->validated(),
            $request->user()
        );

        return redirect()->route('movements.index')->with('success', 'Perpindahan aset berhasil dicatat.');
    }
}
