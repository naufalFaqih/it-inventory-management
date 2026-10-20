<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            ['name' => Role::ROLE_ADMIN, 'label' => 'Administrator'],
            ['name' => Role::ROLE_IT_STAFF, 'label' => 'IT Staff'],
            ['name' => Role::ROLE_VIEWER, 'label' => 'Viewer'],
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role['name']], $role);
        }
    }
}
