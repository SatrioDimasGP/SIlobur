<?php

namespace App\Providers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

use Exception;
use Illuminate\Support\Facades\URL;
use PDOException;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Handle offline database
        // if (config('app.env') === 'local') {
        //     URL::forceScheme('https');
        // }
        // //Use bootstrap 4 for pagination css
        Paginator::useBootstrapFour();
    }
}
