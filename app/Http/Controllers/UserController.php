<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\AuditLog;
use App\Models\Department;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::with(['role', 'department'])->latest();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role_id')) {
            $query->where('role_id', $request->input('role_id'));
        }

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->input('department_id'));
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        $users = $query->paginate(15)->withQueryString();
        $roles = Role::all();
        $departments = Department::all();

        return view('users.index', compact('users', 'roles', 'departments'));
    }

    public function create(): View
    {
        $roles = Role::all();
        $departments = Department::all();

        return view('users.create', compact('roles', 'departments'));
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);
        $data['is_active'] = $request->boolean('is_active', true);

        $user = User::create($data);

        AuditLog::log('create_user', $user, null, [
            'name' => $user->name,
            'email' => $user->email,
            'role_id' => $user->role_id,
            'is_active' => $user->is_active,
        ]);

        return redirect()->route('users.index')->with('success', 'Pengguna berhasil ditambahkan.');
    }

    public function edit(User $user): View
    {
        $roles = Role::all();
        $departments = Department::all();

        return view('users.edit', compact('user', 'roles', 'departments'));
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $data = $request->validated();

        $oldValues = [
            'name' => $user->name,
            'email' => $user->email,
            'role_id' => $user->role_id,
            'department_id' => $user->department_id,
            'is_active' => $user->is_active,
        ];

        if (! empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $data['is_active'] = $request->boolean('is_active');
        $user->update($data);

        AuditLog::log('update_user', $user, $oldValues, [
            'name' => $user->name,
            'email' => $user->email,
            'role_id' => $user->role_id,
            'department_id' => $user->department_id,
            'is_active' => $user->is_active,
        ]);

        return redirect()->route('users.index')->with('success', 'Data pengguna berhasil diperbarui.');
    }

    public function toggleStatus(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat menonaktifkan akun Anda sendiri.');
        }

        $previous = $user->is_active;
        $user->update(['is_active' => ! $previous]);

        AuditLog::log('toggle_user_status', $user, ['is_active' => $previous], ['is_active' => $user->is_active]);

        $statusMsg = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "Akun pengguna {$user->name} berhasil {$statusMsg}.");
    }
}
