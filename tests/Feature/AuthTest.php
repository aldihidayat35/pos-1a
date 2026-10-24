<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    public function test_guest_can_view_login_page(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertSee('Masuk — PasarHub');
        $response->assertSee('Email atau Username');
    }

    public function test_guest_is_redirected_to_login_when_accessing_dashboard(): void
    {
        $response = $this->get('/');

        $response->assertRedirect('/login');
    }

    public function test_user_can_login_with_valid_email(): void
    {
        $response = $this->post('/login', [
            'login' => 'owner@pasarhub.id',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticated();
    }

    public function test_user_can_login_with_valid_username(): void
    {
        $response = $this->post('/login', [
            'login' => 'superadmin',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticated();
    }

    public function test_user_cannot_login_with_invalid_password(): void
    {
        $response = $this->from('/login')->post('/login', [
            'login' => 'owner@pasarhub.id',
            'password' => 'wrong-password-123',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('login');
        $this->assertGuest();
    }

    public function test_inactive_user_cannot_login(): void
    {
        $inactiveUser = User::create([
            'name' => 'Inactive User',
            'username' => 'inactive_tester',
            'email' => 'inactive@pasarhub.id',
            'password' => Hash::make('password'),
            'is_active' => false,
        ]);

        $response = $this->from('/login')->post('/login', [
            'login' => 'inactive_tester',
            'password' => 'password',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('login');
        $this->assertGuest();

        $inactiveUser->delete();
    }

    public function test_authenticated_user_can_logout(): void
    {
        $user = User::where('username', 'owner')->first();

        $response = $this->actingAs($user)->post('/logout');

        $response->assertRedirect('/login');
        $this->assertGuest();
    }
}
