<?php

namespace App\Providers;

use App\Models\Abono;
use App\Models\Controlpago;
use App\Observers\AbonoObserver;
use App\Observers\ControlObserver;
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
        Controlpago::observe(ControlObserver::class);

        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }
    }
}
