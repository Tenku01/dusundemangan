<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\URL;
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
        Model::preventLazyLoading(! app()->isProduction());

        Paginator::defaultView('vendor.pagination.custom');

        $isHttps = config('app.env') === 'production'
            || isset($_SERVER['VERCEL'])
            || isset($_ENV['VERCEL'])
            || getenv('VERCEL') !== false
            || (app()->bound('request') && request()->header('x-forwarded-proto') === 'https');

        if ($isHttps) {
            URL::forceScheme('https');
        }

        if (isset($_SERVER['VERCEL']) || isset($_ENV['VERCEL']) || getenv('VERCEL') !== false) {
            config(['view.compiled' => '/tmp/views']);
        }
    }
}
