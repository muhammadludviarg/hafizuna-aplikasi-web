<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Barryvdh\DomPDF\Facade\Pdf;

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
        Pdf::setOption([
            'enable_html5_parser' => true,
            'isPhpEnabled' => true,
            'defaultFont' => 'DejaVu Sans',
            'isFontSubsettingEnabled' => false,
        ]);
    }
}
