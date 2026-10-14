<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminRedirectTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_accessing_dashboard_is_redirected_to_login(): void
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect(route('login'));
    }

    public function test_admin_redirect_shortcuts(): void
    {
        // /admin -> /dashboard (which then redirects to /login if guest)
        $response = $this->get('/admin');
        $response->assertRedirect(route('dashboard'));

        $response = $this->get('/admin/dashboard');
        $response->assertRedirect(route('dashboard'));

        $response = $this->get('/yonetim');
        $response->assertRedirect(route('dashboard'));
    }

    public function test_admin_user_can_access_dashboard(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)->get('/dashboard');
        $response->assertStatus(200);
    }

    public function test_superadmin_user_can_access_dashboard(): void
    {
        $superadmin = User::factory()->create([
            'is_admin' => false, // even if is_admin is 0, superadmin role should work
            'role' => 'superadmin',
        ]);

        $response = $this->actingAs($superadmin)->get('/dashboard');
        $response->assertStatus(200);
    }

    public function test_regular_customer_cannot_access_dashboard(): void
    {
        $customer = User::factory()->create([
            'is_admin' => false,
            'role' => 'customer',
        ]);

        $response = $this->actingAs($customer)->get('/dashboard');
        $response->assertStatus(403);
    }

    public function test_admin_login_redirects_to_dashboard(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
            'role' => 'admin',
            'password' => bcrypt('cicek123456'),
        ]);

        $response = $this->post('/login', [
            'email' => $admin->email,
            'password' => 'cicek123456',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard'));
    }
}
