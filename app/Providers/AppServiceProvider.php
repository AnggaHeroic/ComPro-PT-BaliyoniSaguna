<?php

namespace App\Providers;

use App\Services\CompanyService;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
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
    }
}
