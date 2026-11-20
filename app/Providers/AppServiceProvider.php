<?php

namespace App\Providers;

use App\Listeners\RecordAdminAccess;
use App\Services\CompanyService;
use App\Services\PageContentService;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Isi halaman dibaca beberapa kali per permintaan (logo, kontak, tiap halaman); satu instance menyimpan hasil bacanya.
        $this->app->singleton(PageContentService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Navbar dan footer ada di setiap halaman publik, jadi datanya disuplai di sini
        // agar tidak diulang di tiap controller. Layout membaca data dari view halaman (@extends).
        View::composer('web.*', function ($view): void {
            $companyService = app(CompanyService::class);

            $view->with([
                'company' => $companyService->profile(),
                'navigation' => $companyService->navigation(),
                'services' => $companyService->services(),
            ]);
        });

        // Logo dan wordmark dipakai di navbar, footer, login, dan menu admin; datanya diatur lewat Isi Halaman > Umum.
        View::composer(['components.web.logo', 'components.web.wordmark'], function ($view): void {
            $view->with('brand', app(CompanyService::class)->brand());
        });

        // Teks ajakan menghubungi (dua variasi) dipakai komponen cta di beberapa halaman; diatur lewat Isi Halaman > Umum.
        View::composer('components.web.cta', function ($view): void {
            $view->with('cta', app(CompanyService::class)->cta());
        });

        // Hanya panel admin yang memakai paginasi; tampilannya mengikuti tema admin.
        Paginator::defaultView('pagination.admin');
        Paginator::defaultSimpleView('pagination.admin');

        // Masuk dan keluar admin masuk ke riwayat aktivitas.
        Event::listen(Login::class, [RecordAdminAccess::class, 'login']);
        Event::listen(Logout::class, [RecordAdminAccess::class, 'logout']);

        // Layout admin dan login hanya butuh profil perusahaan; tanpa navbar, jadi daftar layanan tidak perlu dimuat.
        View::composer(['layouts.guest', 'layouts.app'], function ($view): void {
            $view->with('company', app(CompanyService::class)->profile());
        });
    }
}
