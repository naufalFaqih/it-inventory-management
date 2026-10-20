<?php

namespace App\Http\Controllers;

use App\Http\Requests\LocationRequest;
use App\Models\AuditLog;
use App\Models\Location;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LocationController extends Controller
{
    public function index(Request $request): View
    {
        $query = Location::with(['parent'])->withCount(['assets', 'children']);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('name', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%");
        }

        $locations = $query->latest()->paginate(15)->withQueryString();

        return view('locations.index', compact('locations'));
    }

    public function create(): View
    {
        $parentLocations = Location::whereNull('parent_id')->orderBy('name')->get();
        return view('locations.create', compact('parentLocations'));
    }

    public function store(LocationRequest $request): RedirectResponse
    {
        $location = Location::create($request->validated());

        AuditLog::log('create_location', $location, null, [
            'name' => $location->name,
            'parent_id' => $location->parent_id,
            'description' => $location->description,
        ]);

        return redirect()->route('locations.index')->with('success', 'Lokasi berhasil ditambahkan.');
    }

    public function edit(Location $location): View
    {
        $parentLocations = Location::whereNull('parent_id')
            ->where('id', '!=', $location->id)
            ->orderBy('name')
            ->get();

        return view('locations.edit', compact('location', 'parentLocations'));
    }

    public function update(LocationRequest $request, Location $location): RedirectResponse
    {
        $oldValues = [
            'name' => $location->name,
            'parent_id' => $location->parent_id,
            'description' => $location->description,
        ];

        $location->update($request->validated());

        AuditLog::log('update_location', $location, $oldValues, [
            'name' => $location->name,
            'parent_id' => $location->parent_id,
            'description' => $location->description,
        ]);

        return redirect()->route('locations.index')->with('success', 'Data lokasi berhasil diperbarui.');
    }

    public function destroy(Location $location): RedirectResponse
    {
        if ($location->assets()->exists()) {
            return back()->with('error', 'Lokasi tidak dapat dihapus karena masih digunakan oleh aset.');
        }

        if ($location->children()->exists()) {
            return back()->with('error', 'Lokasi tidak dapat dihapus karena memiliki sub-lokasi/ruangan di bawahnya.');
        }

        $oldValues = ['name' => $location->name];
        $location->delete();

        AuditLog::log('delete_location', $location, $oldValues, null);

        return redirect()->route('locations.index')->with('success', 'Lokasi berhasil dihapus.');
    }
}
