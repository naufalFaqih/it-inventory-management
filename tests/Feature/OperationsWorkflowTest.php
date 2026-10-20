<?php

namespace Tests\Feature;

use App\Models\Asset;
use App\Models\AssetMovement;
use App\Models\Loan;
use App\Models\Location;
use App\Models\MaintenanceRecord;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OperationsWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $staff;
    protected User $viewer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);

        $this->admin = User::where('email', 'admin@inventory.local')->first();
        $this->staff = User::where('email', 'staff@inventory.local')->first();
        $this->viewer = User::where('email', 'viewer@inventory.local')->first();
    }

    public function test_staff_can_record_asset_movement_with_atomic_history(): void
    {
        $asset = Asset::where('status', Asset::STATUS_AVAILABLE)->first();
        $oldLocationId = $asset->location_id;
        $newLocation = Location::where('id', '!=', $oldLocationId)->first();

        $response = $this->actingAs($this->staff)->post(route('movements.store'), [
            'asset_id' => $asset->id,
            'to_location_id' => $newLocation->id,
            'to_user_id' => $this->viewer->id,
            'moved_at' => now()->format('Y-m-d H:i:s'),
            'reason' => 'Perpindahan laptop untuk staf finance',
        ]);

        $response->assertRedirect(route('movements.index'));

        // Check asset updated
        $asset->refresh();
        $this->assertEquals($newLocation->id, $asset->location_id);
        $this->assertEquals($this->viewer->id, $asset->assigned_to);
        $this->assertEquals(Asset::STATUS_ASSIGNED, $asset->status);

        // Check history created
        $this->assertDatabaseHas('asset_movements', [
            'asset_id' => $asset->id,
            'from_location_id' => $oldLocationId,
            'to_location_id' => $newLocation->id,
            'to_user_id' => $this->viewer->id,
            'created_by' => $this->staff->id,
        ]);
    }

    public function test_staff_can_checkout_available_asset_loan(): void
    {
        $asset = Asset::where('status', Asset::STATUS_AVAILABLE)->first();

        $response = $this->actingAs($this->staff)->post(route('loans.store'), [
            'asset_id' => $asset->id,
            'borrower_id' => $this->viewer->id,
            'borrowed_at' => now()->format('Y-m-d H:i:s'),
            'due_at' => now()->addDays(5)->format('Y-m-d'),
            'checkout_condition' => Asset::CONDITION_GOOD,
            'notes' => 'Peminjaman untuk presentasi klien',
        ]);

        $response->assertRedirect(route('loans.index'));

        // Status must be borrowed
        $asset->refresh();
        $this->assertEquals(Asset::STATUS_BORROWED, $asset->status);

        $this->assertDatabaseHas('loans', [
            'asset_id' => $asset->id,
            'borrower_id' => $this->viewer->id,
            'status' => Loan::STATUS_ACTIVE,
        ]);
    }

    public function test_cannot_borrow_already_borrowed_or_maintenance_asset(): void
    {
        // 1. Asset under maintenance
        $maintAsset = Asset::where('status', Asset::STATUS_MAINTENANCE)->first();
        $response1 = $this->actingAs($this->staff)->post(route('loans.store'), [
            'asset_id' => $maintAsset->id,
            'borrower_id' => $this->viewer->id,
            'borrowed_at' => now()->format('Y-m-d H:i:s'),
            'checkout_condition' => Asset::CONDITION_GOOD,
        ]);
        $response1->assertSessionHasErrors('asset_id');

        // 2. Already borrowed asset
        $availAsset = Asset::where('status', Asset::STATUS_AVAILABLE)->first();
        // First checkout
        $this->actingAs($this->staff)->post(route('loans.store'), [
            'asset_id' => $availAsset->id,
            'borrower_id' => $this->viewer->id,
            'borrowed_at' => now()->format('Y-m-d H:i:s'),
            'checkout_condition' => Asset::CONDITION_GOOD,
        ]);

        // Second checkout attempt must fail (BR-03)
        $response2 = $this->actingAs($this->staff)->post(route('loans.store'), [
            'asset_id' => $availAsset->id,
            'borrower_id' => $this->admin->id,
            'borrowed_at' => now()->format('Y-m-d H:i:s'),
            'checkout_condition' => Asset::CONDITION_GOOD,
        ]);
        $response2->assertSessionHasErrors('asset_id');
    }

    public function test_staff_can_checkin_loan_and_update_status(): void
    {
        $asset = Asset::where('status', Asset::STATUS_AVAILABLE)->first();
        // Loan out
        $this->actingAs($this->staff)->post(route('loans.store'), [
            'asset_id' => $asset->id,
            'borrower_id' => $this->viewer->id,
            'borrowed_at' => now()->format('Y-m-d H:i:s'),
            'checkout_condition' => Asset::CONDITION_GOOD,
        ]);

        $loan = Loan::where('asset_id', $asset->id)->first();
        $this->assertTrue($loan->isActive());

        // Return loan
        $returnResponse = $this->actingAs($this->staff)->post(route('loans.return.process', $loan), [
            'returned_at' => now()->format('Y-m-d H:i:s'),
            'return_condition' => Asset::CONDITION_GOOD,
            'notes' => 'Dikembalikan lengkap dengan charger dan tas.',
        ]);

        $returnResponse->assertRedirect(route('loans.index'));

        $loan->refresh();
        $this->assertEquals(Loan::STATUS_RETURNED, $loan->status);

        $asset->refresh();
        $this->assertEquals(Asset::STATUS_AVAILABLE, $asset->status);
    }

    public function test_returning_with_major_damage_auto_triggers_maintenance(): void
    {
        $asset = Asset::where('status', Asset::STATUS_AVAILABLE)->first();
        $this->actingAs($this->staff)->post(route('loans.store'), [
            'asset_id' => $asset->id,
            'borrower_id' => $this->viewer->id,
            'borrowed_at' => now()->format('Y-m-d H:i:s'),
            'checkout_condition' => Asset::CONDITION_GOOD,
        ]);

        $loan = Loan::where('asset_id', $asset->id)->first();

        $this->actingAs($this->staff)->post(route('loans.return.process', $loan), [
            'returned_at' => now()->format('Y-m-d H:i:s'),
            'return_condition' => Asset::CONDITION_MAJOR_DAMAGE,
            'notes' => 'Layar retak akibat terjatuh.',
        ]);

        $asset->refresh();
        $this->assertEquals(Asset::STATUS_MAINTENANCE, $asset->status);
        $this->assertEquals(Asset::CONDITION_MAJOR_DAMAGE, $asset->condition);
    }

    public function test_staff_can_start_and_complete_maintenance(): void
    {
        $asset = Asset::where('status', Asset::STATUS_AVAILABLE)->first();

        // 1. Start Maintenance
        $startResponse = $this->actingAs($this->staff)->post(route('maintenance.store'), [
            'asset_id' => $asset->id,
            'technician_id' => $this->staff->id,
            'issue' => 'Port Ethernet tidak mendeteksi kabel',
            'started_at' => now()->format('Y-m-d H:i:s'),
            'cost' => 150000,
        ]);

        $startResponse->assertRedirect(route('maintenance.index'));

        $asset->refresh();
        $this->assertEquals(Asset::STATUS_MAINTENANCE, $asset->status);

        $maint = MaintenanceRecord::where('asset_id', $asset->id)->first();
        $this->assertTrue($maint->isInProgress());

        // 2. Complete Maintenance
        $completeResponse = $this->actingAs($this->staff)->post(route('maintenance.complete.process', $maint), [
            'action_taken' => 'Penggantian konektor RJ45 internal dan pengujian link speed 1Gbps',
            'completed_at' => now()->format('Y-m-d H:i:s'),
            'cost' => 175000,
            'condition_after' => Asset::CONDITION_GOOD,
            'notes' => 'Perangkat sudah lulus burn-in test.',
        ]);

        $completeResponse->assertRedirect(route('maintenance.index'));

        $maint->refresh();
        $this->assertEquals(MaintenanceRecord::STATUS_COMPLETED, $maint->status);

        $asset->refresh();
        $this->assertEquals(Asset::STATUS_AVAILABLE, $asset->status);
        $this->assertEquals(Asset::CONDITION_GOOD, $asset->condition);
    }

    public function test_viewer_cannot_create_movements(): void
    {
        $response = $this->actingAs($this->viewer)->get(route('movements.create'));
        $response->assertStatus(403);
    }
}
