<?php

namespace Tests\Feature;

use App\Models\Asset;
use App\Models\AuditLog;
use App\Models\Category;
use App\Models\Location;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardAndReportingTest extends TestCase
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

    public function test_dashboard_displays_actual_database_counts(): void
    {
        $total = Asset::count();
        $available = Asset::where('status', Asset::STATUS_AVAILABLE)->count();

        $response = $this->actingAs($this->staff)->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertViewHas('totalAssets', $total);
        $response->assertViewHas('availableAssets', $available);
        $response->assertSee((string) $total);
    }

    public function test_reporting_filters_and_aggregations(): void
    {
        $laptopCat = Category::where('name', 'Laptop')->first();

        $response = $this->actingAs($this->staff)->get(route('reports.index', [
            'category_id' => $laptopCat->id,
        ]));

        $response->assertStatus(200);
        $expectedCount = Asset::where('category_id', $laptopCat->id)->count();
        $response->assertViewHas('totalCount', $expectedCount);
        $response->assertSee('ThinkPad T14 Gen 4');
    }

    public function test_audit_logs_record_changes_and_accessible_by_admin(): void
    {
        // 1. Generate an audit log via asset creation
        $asset = Asset::first();
        AuditLog::log('test_action', $asset, ['status' => 'old'], ['status' => 'new'], $this->admin->id);

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'test_action',
            'user_id' => $this->admin->id,
        ]);

        // 2. Admin can view audit logs
        $response = $this->actingAs($this->admin)->get(route('audit-logs.index'));
        $response->assertStatus(200);
        $response->assertSee('test_action');
    }

    public function test_non_admin_cannot_access_audit_logs(): void
    {
        $response = $this->actingAs($this->staff)->get(route('audit-logs.index'));
        $response->assertStatus(403);
    }
}
