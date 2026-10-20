<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationAndRBACTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('IT Inventory Management');
    }

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $response = $this->post('/login', [
            'email' => 'admin@inventory.local',
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect('/dashboard');
    }

    public function test_users_cannot_authenticate_with_invalid_password(): void
    {
        $response = $this->post('/login', [
            'email' => 'admin@inventory.local',
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('email');
    }

    public function test_inactive_users_cannot_authenticate(): void
    {
        // FR-004: Akun nonaktif tidak dapat login.
        $response = $this->post('/login', [
            'email' => 'inactive@inventory.local',
            'password' => 'password',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('email');
        $this->assertTrue(session('errors')->first('email') === 'Akun Anda telah dinonaktifkan. Silakan hubungi Administrator.');
    }

    public function test_authenticated_users_can_logout(): void
    {
        $user = User::where('email', 'admin@inventory.local')->first();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/login');
    }

    public function test_guest_cannot_access_dashboard(): void
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
    }

    public function test_it_staff_cannot_access_user_management(): void
    {
        // FR-002: Sistem menerapkan role dan pembatasan akses pada route serta aksi.
        $staff = User::where('email', 'staff@inventory.local')->first();

        $response = $this->actingAs($staff)->get('/users');
        $response->assertStatus(403);
    }

    public function test_admin_can_access_user_management_and_create_user(): void
    {
        // FR-003: Administrator dapat membuat akun dan menetapkan role.
        $admin = User::where('email', 'admin@inventory.local')->first();
        $roleStaff = Role::where('name', Role::ROLE_IT_STAFF)->first();

        $response = $this->actingAs($admin)->get('/users');
        $response->assertStatus(200);

        $storeResponse = $this->actingAs($admin)->post('/users', [
            'name' => 'New IT Officer',
            'email' => 'officer@inventory.local',
            'password' => 'password123',
            'role_id' => $roleStaff->id,
            'is_active' => true,
        ]);

        $storeResponse->assertRedirect(route('users.index'));
        $this->assertDatabaseHas('users', [
            'email' => 'officer@inventory.local',
            'role_id' => $roleStaff->id,
            'is_active' => 1,
        ]);
    }

    public function test_admin_can_toggle_user_active_status(): void
    {
        $admin = User::where('email', 'admin@inventory.local')->first();
        $staff = User::where('email', 'staff@inventory.local')->first();

        $this->assertTrue($staff->is_active);

        $response = $this->actingAs($admin)->patch(route('users.toggle-status', $staff));
        $response->assertRedirect();

        $staff->refresh();
        $this->assertFalse($staff->is_active);
    }
}
