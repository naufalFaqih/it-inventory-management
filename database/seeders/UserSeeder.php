<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminRole = Role::where('name', Role::ROLE_ADMIN)->first();
        $itStaffRole = Role::where('name', Role::ROLE_IT_STAFF)->first();
        $viewerRole = Role::where('name', Role::ROLE_VIEWER)->first();

        $itDept = Department::where('name', 'IT Infrastructure')->first();
        $financeDept = Department::where('name', 'Finance & Accounting')->first();

        // Administrator
        User::firstOrCreate(
            ['email' => 'admin@inventory.local'],
            [
                'name' => 'IT Administrator',
                'password' => Hash::make('password'),
                'role_id' => $adminRole->id,
                'department_id' => $itDept?->id,
                'is_active' => true,
            ]
        );

        // IT Staff
        User::firstOrCreate(
            ['email' => 'staff@inventory.local'],
            [
                'name' => 'Ahmad IT Staff',
                'password' => Hash::make('password'),
                'role_id' => $itStaffRole->id,
                'department_id' => $itDept?->id,
                'is_active' => true,
            ]
        );

        // Viewer / General User
        User::firstOrCreate(
            ['email' => 'viewer@inventory.local'],
            [
                'name' => 'Budi Viewer',
                'password' => Hash::make('password'),
                'role_id' => $viewerRole->id,
                'department_id' => $financeDept?->id,
                'is_active' => true,
            ]
        );

        // Inactive User (to test FR-004)
        User::firstOrCreate(
            ['email' => 'inactive@inventory.local'],
            [
                'name' => 'Nonaktif User',
                'password' => Hash::make('password'),
                'role_id' => $itStaffRole->id,
                'department_id' => $itDept?->id,
                'is_active' => false,
            ]
        );
    }
}
