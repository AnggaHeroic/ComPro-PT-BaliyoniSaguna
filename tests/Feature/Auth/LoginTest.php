<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_is_displayed_and_not_indexed(): void
    {
        $this->withoutVite();

        $this->get(route('login'))
            ->assertOk()
            ->assertSee('Masuk')
            ->assertSee('noindex', false);
    }

    public function test_user_can_login_with_valid_credentials(): void
    {
        $user = User::factory()->create();

        $this->post('/login', ['email' => $user->email, 'password' => 'password'])
            ->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_user_cannot_login_with_wrong_password(): void
    {
        $user = User::factory()->create();

        $this->from(route('login'))
            ->post('/login', ['email' => $user->email, 'password' => 'salah'])
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_login_requires_email_and_password(): void
    {
        $this->post('/login', [])->assertSessionHasErrors(['email', 'password']);
    }

    public function test_login_is_throttled_after_too_many_failures(): void
    {
        $user = User::factory()->create();

        foreach (range(1, 5) as $ignored) {
            $this->post('/login', ['email' => $user->email, 'password' => 'salah']);
        }

        $this->post('/login', ['email' => $user->email, 'password' => 'password'])
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_guest_is_redirected_from_admin_to_login(): void
    {
        $this->get(route('admin.dashboard'))->assertRedirect(route('login'));
    }

    public function test_authenticated_user_sees_dashboard_and_is_kept_away_from_login(): void
    {
        $this->withoutVite();
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('admin.dashboard'))->assertOk()->assertSee($user->name)->assertSee('Kategori Layanan')->assertSee('Profil Perusahaan');
        $this->actingAs($user)->get(route('login'))->assertRedirect(route('admin.dashboard'));
    }

    public function test_public_navbar_links_back_to_dashboard_only_for_logged_in_admin(): void
    {
        $this->withoutVite();

        $this->get(route('home'))->assertDontSee('Kembali ke Dashboard Admin');

        $this->actingAs(User::factory()->create())
            ->get(route('home'))
            ->assertSee('Kembali ke Dashboard Admin')
            ->assertSee(route('admin.dashboard'), false);
    }

    public function test_user_can_logout(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('logout'))->assertRedirect(route('login'));

        $this->assertGuest();
    }
}
