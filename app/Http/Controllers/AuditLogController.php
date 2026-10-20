<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuditLogController extends Controller
{
    public function index(): View
    {
        $auditLogs = AuditLog::with('user')->latest('created_at')->paginate(20);
        return view('audit-logs.index', compact('auditLogs'));
    }
}
