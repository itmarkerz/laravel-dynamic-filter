<?php

namespace Itmarkerz\DynamicFilter\Providers;

use Illuminate\Support\ServiceProvider;

class DynamicFilterServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/dynamic-filter.php' => config_path('dynamic-filter.php'),
        ]);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/dynamic-filter.php', 'dynamic-filter'
        );
    }
}
