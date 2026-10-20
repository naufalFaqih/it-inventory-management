<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Category;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $query = Asset::with(['category', 'location', 'assignedUser']);

        if ($request->filled('category_id')) {
            $query->filterByCategory($request->input('category_id'));
        }

        if ($request->filled('location_id')) {
            $query->filterByLocation($request->input('location_id'));
        }

        if ($request->filled('status')) {
            $query->filterByStatus($request->input('status'));
        }

        if ($request->filled('start_date')) {
            $query->whereDate('purchase_date', '>=', $request->input('start_date'));
        }

        if ($request->filled('end_date')) {
            $query->whereDate('purchase_date', '<=', $request->input('end_date'));
        }

        // Summary calculations
        $totalCount = (clone $query)->count();
        $totalValue = (clone $query)->sum('purchase_cost');
        $averageValue = $totalCount > 0 ? $totalValue / $totalCount : 0;

        $statusBreakdown = (clone $query)->selectRaw('status, count(*) as count, sum(purchase_cost) as total_cost')
            ->groupBy('status')
            ->get();

        $assets = $query->latest('purchase_date')->paginate(20)->withQueryString();

        $categories = Category::orderBy('name')->get();
        $locations = Location::orderBy('name')->get();

        return view('reports.index', compact(
            'assets',
            'categories',
            'locations',
            'totalCount',
            'totalValue',
            'averageValue',
            'statusBreakdown'
        ));
    }
}
