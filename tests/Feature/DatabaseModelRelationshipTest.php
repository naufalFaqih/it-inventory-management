<?php

namespace Tests\Feature;

use App\Models\Asset;
use App\Models\AssetMovement;
use App\Models\Category;
use App\Models\Department;
use App\Models\Loan;
use App\Models\Location;
use App\Models\MaintenanceRecord;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DatabaseModelRelationshipTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeders_populate_initial_data_correctly(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->assertDatabaseHas('roles', ['name' => Role::ROLE_ADMIN]);
        $this->assertDatabaseHas('roles', ['name' => Role::ROLE_IT_STAFF]);
        $this->assertDatabaseHas('roles', ['name' => Role::ROLE_VIEWER]);

        $this->assertDatabaseHas('users', ['email' => 'admin@inventory.local']);
        $this->assertDatabaseHas('users', ['email' => 'staff@inventory.local']);
        $this->assertDatabaseHas('users', ['email' => 'inactive@inventory.local', 'is_active' => false]);

        $this->assertDatabaseHas('categories', ['name' => 'Laptop']);
        $this->assertDatabaseHas('categories', ['name' => 'Server']);

        $this->assertDatabaseHas('locations', ['name' => 'Kantor Pusat']);
        $this->assertDatabaseHas('locations', ['name' => 'Ruang Server Lantai 1']);

        $this->assertDatabaseHas('assets', ['asset_tag' => 'AST-2026-0001']);
        $this->assertDatabaseHas('assets', ['asset_tag' => 'AST-2026-0002']);
    }

    public function test_asset_relationships_and_soft_delete(): void
    {
        $this->seed(DatabaseSeeder::class);

        $asset = Asset::where('asset_tag', 'AST-2026-0001')->first();
        $this->assertNotNull($asset);
        $this->assertInstanceOf(Category::class, $asset->category);
        $this->assertInstanceOf(Location::class, $asset->location);
        $this->assertInstanceOf(User::class, $asset->assignedUser);
        $this->assertInstanceOf(User::class, $asset->creator);

        // Soft delete test
        $asset->delete();
        $this->assertSoftDeleted($asset);
        $this->assertDatabaseHas('assets', ['id' => $asset->id]);
    }

    public function test_asset_tag_must_be_unique(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->expectException(\Illuminate\Database\QueryException::class);

        $admin = User::first();
        $cat = Category::first();
        $loc = Location::first();

        Asset::create([
            'asset_tag' => 'AST-2026-0001', // Duplicate tag
            'name' => 'Duplicate Device',
            'category_id' => $cat->id,
            'brand' => 'Test',
            'model' => 'Test Model',
            'location_id' => $loc->id,
            'condition' => 'good',
            'status' => 'available',
            'created_by' => $admin->id,
        ]);
    }

    public function test_asset_movement_loan_and_maintenance_relations(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = User::where('email', 'admin@inventory.local')->first();
        $staff = User::where('email', 'staff@inventory.local')->first();
        $viewer = User::where('email', 'viewer@inventory.local')->first();
        $asset = Asset::where('asset_tag', 'AST-2026-0002')->first();
        $loc1 = Location::first();
        $loc2 = Location::skip(1)->first();

        // 1. Movement
        $movement = AssetMovement::create([
            'asset_id' => $asset->id,
            'from_location_id' => $loc1->id,
            'to_location_id' => $loc2->id,
            'from_user_id' => null,
            'to_user_id' => $viewer->id,
            'moved_at' => now(),
            'reason' => 'Dipindahkan untuk kebutuhan audit',
            'created_by' => $staff->id,
        ]);
        $this->assertInstanceOf(Asset::class, $movement->asset);
        $this->assertEquals($viewer->id, $movement->toUser->id);

        // 2. Loan
        $loan = Loan::create([
            'asset_id' => $asset->id,
            'borrower_id' => $viewer->id,
            'borrowed_at' => now(),
            'due_at' => now()->addDays(7),
            'checkout_condition' => 'good',
            'status' => Loan::STATUS_ACTIVE,
            'created_by' => $staff->id,
        ]);
        $this->assertTrue($loan->isActive());
        $this->assertEquals($viewer->id, $loan->borrower->id);

        // 3. Maintenance
        $maintenance = MaintenanceRecord::create([
            'asset_id' => $asset->id,
            'technician_id' => $staff->id,
            'issue' => 'Kipas pendingin berbunyi bising',
            'cost' => 250000.00,
            'started_at' => now(),
            'status' => MaintenanceRecord::STATUS_IN_PROGRESS,
            'created_by' => $admin->id,
        ]);
        $this->assertTrue($maintenance->isInProgress());
        $this->assertEquals($staff->id, $maintenance->technician->id);

        // Has transaction history check
        $this->assertTrue($asset->hasTransactionHistory());
    }
}
