<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetMovement;
use App\Models\Category;
use App\Models\Loan;
use App\Models\Location;
use App\Models\MaintenanceRecord;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        // KPI calculations from database (BR-10)
        $totalAssets = Asset::count();
        $availableAssets = Asset::where('status', Asset::STATUS_AVAILABLE)->count();
        $assignedAssets = Asset::where('status', Asset::STATUS_ASSIGNED)->count();
        $borrowedAssets = Asset::where('status', Asset::STATUS_BORROWED)->count();
        $maintenanceAssets = Asset::where('status', Asset::STATUS_MAINTENANCE)->count();
        $retiredAssets = Asset::where('status', Asset::STATUS_RETIRED)->count();
        $totalCost = Asset::sum('purchase_cost');

        $activeLoans = Loan::with(['asset', 'borrower'])
            ->where('status', Loan::STATUS_ACTIVE)
            ->latest('borrowed_at')
            ->limit(5)
            ->get();

        $activeMaintenance = MaintenanceRecord::with(['asset', 'technician'])
            ->where('status', MaintenanceRecord::STATUS_IN_PROGRESS)
            ->latest('started_at')
            ->limit(5)
            ->get();

        $recentMovements = AssetMovement::with(['asset', 'fromLocation', 'toLocation', 'toUser', 'creator'])
            ->latest('moved_at')
            ->limit(6)
            ->get();

        $categories = Category::withCount('assets')->orderBy('assets_count', 'desc')->get();
        $locations = Location::whereNull('parent_id')->withCount('assets')->orderBy('assets_count', 'desc')->get();

        return view('dashboard', compact(
            'totalAssets',
            'availableAssets',
            'assignedAssets',
            'borrowedAssets',
            'maintenanceAssets',
            'retiredAssets',
            'totalCost',
            'activeLoans',
            'activeMaintenance',
            'recentMovements',
            'categories',
            'locations'
        ));
    }
}
