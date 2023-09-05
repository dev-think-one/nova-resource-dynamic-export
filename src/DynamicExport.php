<?php

namespace NovaResourceDynamicExport;

use Closure;

class DynamicExport
{
    /**
     * Indicates if NovaExportConfiguration migrations will be run.
     */
    public static bool $runsMigrations = true;

    /**
     * Indicates if NovaExportConfiguration will provide download route.
     */
    public static bool $useRoutes = true;

    /**
     * Route Configuration callback
     */
    public static Closure|null $routeConfigurationCallback = null;

    public static function ignoreMigrations(): static
    {
        static::$runsMigrations = false;

        return new static;
    }

    public static function withoutRoutes(): static
    {
        static::$useRoutes = false;

        return new static;
    }

    public static function routeConfiguration(?\Closure $callback = null): mixed
    {
        if($callback) {
            static::$routeConfigurationCallback = $callback;

            return new static;
        }

        if(is_callable(static::$routeConfigurationCallback)) {
            return call_user_func(static::$routeConfigurationCallback);
        }

        return [
            'prefix'     => 'downloads/exports',
            'middleware' => config('nova.middleware'),
        ];
    }

}
