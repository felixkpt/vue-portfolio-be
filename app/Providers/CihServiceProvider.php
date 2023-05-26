<?php

namespace App\Providers;

use App\Cih\Commands\Autogenerate\AutoGenerateList;
use App\Cih\Commands\BackupDatabase;
use App\Cih\Commands\CachePermissions;
use App\Cih\Commands\CreateSuperAdmin;
use App\Cih\Http\Middleware\ShAuth;
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
        require base_path('app/Repositories/helperrepo.php');
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

        if ($this->app->runningInConsole()) {
            $this->commands([
                ...AutoGenerateList::commands(),
                BackupDatabase::class,
                // Initialize::class,
                CreateSuperAdmin::class,
                CachePermissions::class
            ]);
        }

        $router = $this->app->make(Router::class);
        $router->aliasMiddleware('sh_auth', ShAuth::class);
        $this->loadRoutesFrom(base_path('app/Cih/routes/driver.php'));
    }
}
