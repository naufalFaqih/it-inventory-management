<?php

namespace App\Http\Controllers;

use App\Http\Requests\DepartmentRequest;
use App\Models\AuditLog;
use App\Models\Department;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DepartmentController extends Controller
{
    public function index(Request $request): View
    {
        $query = Department::withCount('users');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('name', 'like', "%{$search}%");
        }

        $departments = $query->latest()->paginate(15)->withQueryString();

        return view('departments.index', compact('departments'));
    }

    public function create(): View
    {
        return view('departments.create');
    }

    public function store(DepartmentRequest $request): RedirectResponse
    {
        $dept = Department::create($request->validated());

        AuditLog::log('create_department', $dept, null, ['name' => $dept->name]);

        return redirect()->route('departments.index')->with('success', 'Departemen berhasil ditambahkan.');
    }

    public function edit(Department $department): View
    {
        return view('departments.edit', compact('department'));
    }

    public function update(DepartmentRequest $request, Department $department): RedirectResponse
    {
        $oldValues = ['name' => $department->name];
        $department->update($request->validated());

        AuditLog::log('update_department', $department, $oldValues, ['name' => $department->name]);

        return redirect()->route('departments.index')->with('success', 'Departemen berhasil diperbarui.');
    }

    public function destroy(Department $department): RedirectResponse
    {
        if ($department->users()->exists()) {
            return back()->with('error', 'Departemen tidak dapat dihapus karena masih memiliki pengguna terkait.');
        }

        $oldValues = ['name' => $department->name];
        $department->delete();

        AuditLog::log('delete_department', $department, $oldValues, null);

        return redirect()->route('departments.index')->with('success', 'Departemen berhasil dihapus.');
    }
}
