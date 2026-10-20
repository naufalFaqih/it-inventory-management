<?php

namespace App\Http\Controllers;

use App\Models\MaintenanceRecord;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MaintenanceController extends Controller
{
    public function index(): View
    {
        $maintenanceRecords = MaintenanceRecord::with(['asset', 'technician', 'creator'])->latest('started_at')->paginate(15);
        return view('maintenance.index', compact('maintenanceRecords'));
    }
}
