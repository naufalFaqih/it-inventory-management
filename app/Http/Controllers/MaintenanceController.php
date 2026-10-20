<?php

namespace App\Http\Controllers;

use App\Http\Requests\CompleteMaintenanceRequest;
use App\Http\Requests\StoreMaintenanceRequest;
use App\Models\Asset;
use App\Models\MaintenanceRecord;
use App\Models\User;
use App\Services\MaintenanceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MaintenanceController extends Controller
{
    public function __construct(
        protected MaintenanceService $maintenanceService
    ) {}

    public function index(Request $request): View
    {
        $query = MaintenanceRecord::with(['asset', 'technician', 'creator']);

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('issue', 'like', "%{$search}%")
                    ->orWhere('action_taken', 'like', "%{$search}%")
                    ->orWhereHas('asset', function ($sq) use ($search) {
                        $sq->where('name', 'like', "%{$search}%")
                            ->orWhere('asset_tag', 'like', "%{$search}%");
                    });
            });
        }

        $records = $query->latest('started_at')->paginate(15)->withQueryString();

        return view('maintenance.index', compact('records'));
    }

    public function create(Request $request): View
    {
        $selectedAsset = $request->filled('asset_id') ? Asset::find($request->input('asset_id')) : null;
        // Assets that are not already under maintenance or retired
        $eligibleAssets = Asset::whereNotIn('status', [Asset::STATUS_MAINTENANCE, Asset::STATUS_RETIRED, Asset::STATUS_BORROWED])
            ->orderBy('name')
            ->get();
        $technicians = User::where('is_active', true)->orderBy('name')->get();

        return view('maintenance.create', compact('selectedAsset', 'eligibleAssets', 'technicians'));
    }

    public function store(StoreMaintenanceRequest $request): RedirectResponse
    {
        $asset = Asset::findOrFail($request->input('asset_id'));

        $this->maintenanceService->start(
            $asset,
            $request->validated(),
            $request->user()
        );

        return redirect()->route('maintenance.index')->with('success', 'Catatan pemeliharaan/perbaikan berhasil dibuat.');
    }

    public function completeForm(MaintenanceRecord $maintenance): View
    {
        if ($maintenance->status !== MaintenanceRecord::STATUS_IN_PROGRESS) {
            return redirect()->route('maintenance.index')->with('error', 'Pekerjaan perbaikan ini sudah diselesaikan.');
        }

        return view('maintenance.complete', compact('maintenance'));
    }

    public function completeProcess(CompleteMaintenanceRequest $request, MaintenanceRecord $maintenance): RedirectResponse
    {
        $this->maintenanceService->complete(
            $maintenance,
            $request->validated(),
            $request->user()
        );

        return redirect()->route('maintenance.index')->with('success', 'Pekerjaan pemeliharaan/perbaikan telah diselesaikan.');
    }
}
