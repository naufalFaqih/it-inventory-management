<?php

namespace Tests\Feature;

use App\Models\Asset;
use App\Models\Category;
use App\Models\Department;
use App\Models\Location;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MasterDataTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $staff;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);

        $this->admin = User::where('email', 'admin@inventory.local')->first();
        $this->staff = User::where('email', 'staff@inventory.local')->first();
    }

    public function test_non_admin_cannot_manage_categories(): void
    {
        $response = $this->actingAs($this->staff)->get('/categories');
        $response->assertStatus(403);
    }

    public function test_admin_can_crud_category(): void
    {
        // 1. Create
        $response = $this->actingAs($this->admin)->post('/categories', [
            'name' => 'Tablet',
            'description' => 'Perangkat tablet operasional lapangan',
        ]);
        $response->assertRedirect(route('categories.index'));
        $this->assertDatabaseHas('categories', ['name' => 'Tablet']);

        $category = Category::where('name', 'Tablet')->first();

        // 2. Update
        $updateResponse = $this->actingAs($this->admin)->put("/categories/{$category->id}", [
            'name' => 'Tablet & iPad',
            'description' => 'Perangkat tablet dan iPad operasional',
        ]);
        $updateResponse->assertRedirect(route('categories.index'));
        $this->assertDatabaseHas('categories', ['name' => 'Tablet & iPad']);

        // 3. Delete (no assets attached)
        $deleteResponse = $this->actingAs($this->admin)->delete("/categories/{$category->id}");
        $deleteResponse->assertRedirect(route('categories.index'));
        $this->assertDatabaseMissing('categories', ['name' => 'Tablet & iPad']);
    }

    public function test_admin_cannot_delete_category_with_assets(): void
    {
        $laptopCat = Category::where('name', 'Laptop')->first();
        $this->assertTrue($laptopCat->assets()->exists());

        $response = $this->actingAs($this->admin)->delete("/categories/{$laptopCat->id}");
        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('categories', ['id' => $laptopCat->id]);
    }

    public function test_admin_can_crud_location_with_hierarchy(): void
    {
        // 1. Create Parent Location
        $parentResponse = $this->actingAs($this->admin)->post('/locations', [
            'name' => 'Gedung Data Center BSD',
            'description' => 'Fasilitas Disaster Recovery Center',
        ]);
        $parentResponse->assertRedirect(route('locations.index'));
        $this->assertDatabaseHas('locations', ['name' => 'Gedung Data Center BSD']);

        $parent = Location::where('name', 'Gedung Data Center BSD')->first();

        // 2. Create Child Location
        $childResponse = $this->actingAs($this->admin)->post('/locations', [
            'name' => 'Rack A-01 DRC',
            'parent_id' => $parent->id,
            'description' => 'Server rack utama DRC',
        ]);
        $childResponse->assertRedirect(route('locations.index'));
        $this->assertDatabaseHas('locations', [
            'name' => 'Rack A-01 DRC',
            'parent_id' => $parent->id,
        ]);

        $child = Location::where('name', 'Rack A-01 DRC')->first();

        // 3. Parent cannot be deleted while having children
        $deleteParent = $this->actingAs($this->admin)->delete("/locations/{$parent->id}");
        $deleteParent->assertSessionHas('error');
        $this->assertDatabaseHas('locations', ['id' => $parent->id]);

        // 4. Delete child first, then parent
        $this->actingAs($this->admin)->delete("/locations/{$child->id}");
        $this->assertDatabaseMissing('locations', ['id' => $child->id]);

        $this->actingAs($this->admin)->delete("/locations/{$parent->id}");
        $this->assertDatabaseMissing('locations', ['id' => $parent->id]);
    }

    public function test_admin_can_crud_department(): void
    {
        $response = $this->actingAs($this->admin)->post('/departments', [
            'name' => 'Cyber Security Operations',
        ]);
        $response->assertRedirect(route('departments.index'));
        $this->assertDatabaseHas('departments', ['name' => 'Cyber Security Operations']);

        $dept = Department::where('name', 'Cyber Security Operations')->first();

        $this->actingAs($this->admin)->put("/departments/{$dept->id}", [
            'name' => 'InfoSec & Compliance',
        ]);
        $this->assertDatabaseHas('departments', ['name' => 'InfoSec & Compliance']);

        $this->actingAs($this->admin)->delete("/departments/{$dept->id}");
        $this->assertDatabaseMissing('departments', ['name' => 'InfoSec & Compliance']);
    }
}
