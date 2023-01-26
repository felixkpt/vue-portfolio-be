<?php

namespace App\Providers;

use App\Cih\App\Http\Middleware\ShAuth;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class CihServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        /** force all urls in production to be secure */
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }

        $router = $this->app->make(Router::class);
        $router->aliasMiddleware('sh_auth', ShAuth::class);   
        $this->loadRoutesFrom(base_path('app/Cih/routes/driver.php'));
        $this->loadMigrationsFrom(base_path('app/Cih/migrations'));

    }
}
