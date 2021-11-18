<?php

namespace App\Providers;

use App\Models\SuggestSettings;
use Illuminate\Support\ServiceProvider;

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
        $this->app->singleton(SuggestSettings::class, function () {
            return SuggestSettings::query()->firstOrFail();
        });
    }
}
