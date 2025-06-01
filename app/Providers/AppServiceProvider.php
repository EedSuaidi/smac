<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\CryptoPriceService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(CryptoPriceService::class, function () { // Perbarui singleton
            return new CryptoPriceService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
