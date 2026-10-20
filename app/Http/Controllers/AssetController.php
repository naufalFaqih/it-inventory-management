<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAssetRequest;
use App\Http\Requests\UpdateAssetRequest;
use App\Models\Asset;
use App\Models\AuditLog;
use App\Models\Category;
use App\Models\Location;
use App\Models\User;
use App\Services\AssetService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AssetController extends Controller
{
    public function __construct(
        protected AssetService $assetService
    ) {}

    public function index(Request $request): View
    {
        $query = Asset::with(['category', 'location', 'assignedUser']);

        if ($request->filled('search')) {
            $query->search($request->input('search'));
        }

        if ($request->filled('category_id')) {
            $query->filterByCategory($request->input('category_id'));
        }

        if ($request->filled('location_id')) {
            $query->filterByLocation($request->input('location_id'));
        }

        if ($request->filled('status')) {
            $query->filterByStatus($request->input('status'));
        }

        if ($request->filled('condition')) {
            $query->where('condition', $request->input('condition'));
        }

        $sortField = $request->input('sort', 'created_at');
        $sortDir = $request->input('direction', 'desc');
        if (in_array($sortField, ['asset_tag', 'name', 'status', 'created_at'], true)) {
            $query->orderBy($sortField, $sortDir === 'asc' ? 'asc' : 'desc');
        } else {
            $query->latest();
        }

        $assets = $query->paginate(15)->withQueryString();
        $categories = Category::orderBy('name')->get();
        $locations = Location::orderBy('name')->get();

        return view('assets.index', compact('assets', 'categories', 'locations'));
    }

    public function create(): View
    {
        $suggestedTag = $this->assetService->generateAssetTag();
        $categories = Category::orderBy('name')->get();
        $locations = Location::orderBy('name')->get();
        $users = User::where('is_active', true)->orderBy('name')->get();

        return view('assets.create', compact('suggestedTag', 'categories', 'locations', 'users'));
    }

    public function store(StoreAssetRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if (empty($data['asset_tag'])) {
            $data['asset_tag'] = $this->assetService->generateAssetTag();
        }

        $data['created_by'] = auth()->id();

        $asset = DB::transaction(function () use ($data) {
            $created = Asset::create($data);

            AuditLog::log('create_asset', $created, null, [
                'asset_tag' => $created->asset_tag,
                'name' => $created->name,
                'status' => $created->status,
                'location_id' => $created->location_id,
            ]);

            return $created;
        });

        return redirect()->route('assets.show', $asset)->with('success', 'Aset baru berhasil didaftarkan.');
    }

    public function show(Asset $asset): View
    {
        $asset->load([
            'category',
            'location',
            'assignedUser',
            'creator',
            'movements.fromLocation',
            'movements.toLocation',
            'movements.toUser',
            'movements.creator',
            'loans.borrower',
            'loans.creator',
            'maintenanceRecords.technician',
            'maintenanceRecords.creator',
        ]);

        return view('assets.show', compact('asset'));
    }

    public function edit(Asset $asset): View
    {
        $categories = Category::orderBy('name')->get();
        $locations = Location::orderBy('name')->get();
        $users = User::where('is_active', true)->orderBy('name')->get();

        return view('assets.edit', compact('asset', 'categories', 'locations', 'users'));
    }

    public function update(UpdateAssetRequest $request, Asset $asset): RedirectResponse
    {
        $data = $request->validated();

        DB::transaction(function () use ($asset, $data) {
            $oldValues = $asset->only([
                'name', 'category_id', 'brand', 'model', 'serial_number',
                'location_id', 'assigned_to', 'condition', 'status', 'purchase_cost',
            ]);

            $asset->update($data);

            AuditLog::log('update_asset', $asset, $oldValues, $asset->only([
                'name', 'category_id', 'brand', 'model', 'serial_number',
                'location_id', 'assigned_to', 'condition', 'status', 'purchase_cost',
            ]));
        });

        return redirect()->route('assets.show', $asset)->with('success', 'Informasi aset berhasil diperbarui.');
    }

    public function destroy(Asset $asset): RedirectResponse
    {
        // BR-06: Aset yang memiliki histori transaksi tidak dihapus permanen melalui operasi normal
        if ($asset->hasTransactionHistory()) {
            // Use retire workflow instead of hard delete
            $this->assetService->retire($asset, 'Dinonaktifkan via tombol hapus (memiliki histori transaksi).');
            return redirect()->route('assets.index')->with('success', 'Aset dinonaktifkan (retired) karena memiliki riwayat transaksi.');
        }

        $oldValues = ['asset_tag' => $asset->asset_tag, 'name' => $asset->name];
        $asset->delete(); // Soft delete

        AuditLog::log('delete_asset', $asset, $oldValues, null);

        return redirect()->route('assets.index')->with('success', 'Aset berhasil dihapus.');
    }

    public function retire(Request $request, Asset $asset): RedirectResponse
    {
        $request->validate([
            'reason' => ['nullable', 'string', 'max:500'],
        ]);

        $this->assetService->retire($asset, $request->input('reason'));

        return back()->with('success', "Aset {$asset->asset_tag} berhasil dinonaktifkan (retired).");
    }
}
