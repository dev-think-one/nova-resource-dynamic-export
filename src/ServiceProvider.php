<?php

namespace NovaResourceDynamicExport;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    /**
     * @inheritDoc
     */
    public function boot()
    {
        if (DynamicExport::$useRoutes) {
            $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        }

        if ($this->app->runningInConsole()) {
            if (DynamicExport::$runsMigrations) {
                $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
            }

            $this->publishes([
                __DIR__ . '/../config/nova-resource-dynamic-export.php' => config_path('nova-resource-dynamic-export.php'),
            ], 'config');
        }
    }

    /**
     * @inheritDoc
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/nova-resource-dynamic-export.php', 'nova-resource-dynamic-export');
    }
}
