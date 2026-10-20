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
        $totalAssets = Asset::count();
        $availableAssets = Asset::where('status', Asset::STATUS_AVAILABLE)->count();
        $assignedAssets = Asset::where('status', Asset::STATUS_ASSIGNED)->count();
        $borrowedAssets = Asset::where('status', Asset::STATUS_BORROWED)->count();
        $maintenanceAssets = Asset::where('status', Asset::STATUS_MAINTENANCE)->count();
        $retiredAssets = Asset::where('status', Asset::STATUS_RETIRED)->count();

        $recentMovements = AssetMovement::with(['asset', 'fromLocation', 'toLocation', 'toUser'])
            ->latest('moved_at')
            ->limit(5)
            ->get();

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

        $categoryDistribution = Category::withCount('assets')->get();
        $locationDistribution = Location::whereNull('parent_id')->withCount('assets')->get();

        return view('dashboard', compact(
            'totalAssets',
            'availableAssets',
            'assignedAssets',
            'borrowedAssets',
            'maintenanceAssets',
            'retiredAssets',
            'recentMovements',
            'activeLoans',
            'activeMaintenance',
            'categoryDistribution',
            'locationDistribution'
        ));
    }
}
