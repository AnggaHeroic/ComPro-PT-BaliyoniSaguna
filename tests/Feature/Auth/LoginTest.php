<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Database\Seeders\SiteContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(SiteContentSeeder::class);
    }

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

    public function test_login_is_locked_after_three_failures_regardless_of_email(): void
    {
        $this->withoutVite();
        $user = User::factory()->create();

        $this->post('/login', ['email' => $user->email, 'password' => 'salah']);
        $this->post('/login', ['email' => 'lain@example.com', 'password' => 'salah']);

        // Kegagalan ketiga memicu kunci, dengan email apa pun. Peringatannya tampil di halaman login dan hilang sendiri setelah 10 detik
        // (halaman dibaca sebelum assertSessionHasErrors, karena pemeriksaan itu ikut menghabiskan data flash).
        $this->post('/login', ['email' => 'lagi@example.com', 'password' => 'salah']);
        $this->get('/login')->assertSee('Terlalu banyak percobaan masuk. Coba lagi dalam beberapa menit.')->assertSee('setTimeout(() => show = false, 10000)', false);

        // Kata sandi benar pun ditolak selama kunci.
        $this->post('/login', ['email' => $user->email, 'password' => 'password'])
            ->assertSessionHasErrors(['lockout' => 'Terlalu banyak percobaan masuk. Coba lagi dalam beberapa menit.']);
        $this->assertGuest();
    }

    public function test_two_failures_do_not_lock_the_login(): void
    {
        $user = User::factory()->create();

        $this->post('/login', ['email' => $user->email, 'password' => 'salah']);
        $this->post('/login', ['email' => $user->email, 'password' => 'salah']);
        $this->post('/login', ['email' => $user->email, 'password' => 'password'])->assertRedirect();

        $this->assertAuthenticated();
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
