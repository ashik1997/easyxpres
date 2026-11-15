<?php

namespace App\Providers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
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
        $app = Schema::hasTable('app_setups')
            ? DB::table('app_setups')->where('id', 1)->first()
            : null;
        view()->composer(
            ['*'],
            // ['layouts.*', 'livewire.*'],
            function ($view) use ($app) {
                $view->with(['app' => $app]);
            }
        );
    }
}
