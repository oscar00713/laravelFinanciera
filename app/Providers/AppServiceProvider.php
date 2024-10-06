<?php

namespace App\Providers;

use App\Models\Abono;
use App\Observers\AbonoObserver;
use Illuminate\Support\Facades\URL;
use Illuminate\Database\Eloquent\Model;
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
        Model::unguard();

        Abono::observe(AbonoObserver::class);

        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }
    }
}
