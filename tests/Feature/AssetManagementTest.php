<?php

namespace Tests\Feature;

use App\Models\Asset;
use App\Models\AssetMovement;
use App\Models\Category;
use App\Models\Location;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AssetManagementTest extends TestCase
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

    public function test_staff_can_view_asset_list_and_filter(): void
    {
        $response = $this->actingAs($this->staff)->get(route('assets.index', [
            'search' => 'ThinkPad',
            'status' => Asset::STATUS_ASSIGNED,
        ]));

        $response->assertStatus(200);
        $response->assertSee('ThinkPad T14 Gen 4');
    }

    public function test_staff_can_create_asset_with_manual_tag(): void
    {
        $cat = Category::first();
        $loc = Location::first();

        $response = $this->actingAs($this->staff)->post(route('assets.store'), [
            'asset_tag' => 'AST-MANUAL-999',
            'name' => 'Dell Latitude 5440',
            'category_id' => $cat->id,
            'brand' => 'Dell',
            'model' => 'Latitude 5440 Core i5',
            'serial_number' => 'DL-SN-999',
            'location_id' => $loc->id,
            'condition' => Asset::CONDITION_GOOD,
            'status' => Asset::STATUS_AVAILABLE,
            'purchase_date' => '2026-03-01',
            'purchase_cost' => 14000000,
        ]);

        $this->assertDatabaseHas('assets', [
            'asset_tag' => 'AST-MANUAL-999',
            'name' => 'Dell Latitude 5440',
        ]);
        $response->assertRedirect();
    }

    public function test_duplicate_asset_tag_is_rejected(): void
    {
        $cat = Category::first();
        $loc = Location::first();

        $response = $this->actingAs($this->staff)->post(route('assets.store'), [
            'asset_tag' => 'AST-2026-0001', // Existing tag
            'name' => 'Duplicate Device',
            'category_id' => $cat->id,
            'brand' => 'Brand',
            'model' => 'Model',
            'location_id' => $loc->id,
            'condition' => Asset::CONDITION_GOOD,
            'status' => Asset::STATUS_AVAILABLE,
        ]);

        $response->assertSessionHasErrors('asset_tag');
    }

    public function test_auto_generates_tag_when_left_empty(): void
    {
        $cat = Category::first();
        $loc = Location::first();

        $response = $this->actingAs($this->staff)->post(route('assets.store'), [
            'asset_tag' => '', // Auto generate
            'name' => 'HP LaserJet Pro M404n',
            'category_id' => $cat->id,
            'brand' => 'HP',
            'model' => 'M404n',
            'location_id' => $loc->id,
            'condition' => Asset::CONDITION_GOOD,
            'status' => Asset::STATUS_AVAILABLE,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('assets', [
            'name' => 'HP LaserJet Pro M404n',
        ]);

        $created = Asset::where('name', 'HP LaserJet Pro M404n')->first();
        $this->assertNotNull($created->asset_tag);
        $this->assertStringStartsWith('AST-' . date('Y') . '-', $created->asset_tag);
    }

    public function test_staff_can_view_detail_and_update_asset(): void
    {
        $asset = Asset::where('asset_tag', 'AST-2026-0001')->first();

        $showResponse = $this->actingAs($this->staff)->get(route('assets.show', $asset));
        $showResponse->assertStatus(200);
        $showResponse->assertSee($asset->asset_tag);

        $updateResponse = $this->actingAs($this->staff)->put(route('assets.update', $asset), [
            'name' => 'ThinkPad T14 Gen 4 (Upgraded to 32GB RAM)',
            'category_id' => $asset->category_id,
            'brand' => $asset->brand,
            'model' => $asset->model,
            'serial_number' => $asset->serial_number,
            'location_id' => $asset->location_id,
            'condition' => $asset->condition,
            'status' => $asset->status,
        ]);

        $updateResponse->assertRedirect(route('assets.show', $asset));
        $this->assertDatabaseHas('assets', [
            'id' => $asset->id,
            'name' => 'ThinkPad T14 Gen 4 (Upgraded to 32GB RAM)',
        ]);
    }

    public function test_asset_retirement(): void
    {
        $asset = Asset::where('asset_tag', 'AST-2026-0001')->first();

        $response = $this->actingAs($this->staff)->patch(route('assets.retire', $asset), [
            'reason' => 'Perangkat sudah usang dan diganti model baru.',
        ]);

        $response->assertRedirect();
        $asset->refresh();
        $this->assertEquals(Asset::STATUS_RETIRED, $asset->status);
        $this->assertNull($asset->assigned_to);
    }

    public function test_asset_with_transaction_history_is_protected_on_delete(): void
    {
        $asset = Asset::where('asset_tag', 'AST-2026-0001')->first();

        // Create transaction history
        AssetMovement::create([
            'asset_id' => $asset->id,
            'from_location_id' => $asset->location_id,
            'to_location_id' => Location::skip(1)->first()->id,
            'moved_at' => now(),
            'reason' => 'Relokasi ruang kerja',
            'created_by' => $this->staff->id,
        ]);

        $this->assertTrue($asset->hasTransactionHistory());

        // Deleting it should retire rather than hard delete (BR-06)
        $response = $this->actingAs($this->staff)->delete(route('assets.destroy', $asset));
        $response->assertRedirect(route('assets.index'));

        $asset->refresh();
        $this->assertEquals(Asset::STATUS_RETIRED, $asset->status);
        $this->assertNull($asset->deleted_at);
    }

    public function test_viewer_cannot_modify_assets(): void
    {
        $asset = Asset::first();

        $response = $this->actingAs($this->viewer)->delete(route('assets.destroy', $asset));
        $response->assertStatus(403);
    }
}
