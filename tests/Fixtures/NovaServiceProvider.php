<?php

namespace NovaResourceDynamicExport\Tests\Fixtures;

use Illuminate\Support\Facades\Gate;
use Laravel\Nova\Nova;
use Laravel\Nova\NovaApplicationServiceProvider;
use NovaResourceDynamicExport\CustomResourcesExport;
use NovaResourceDynamicExport\Nova\Resources\ExportStoredFile;
use NovaResourceDynamicExport\Tests\Fixtures\Exports\PostsWithTagBreaking;
use NovaResourceDynamicExport\Tests\Fixtures\Nova\Resources\Post;

class NovaServiceProvider extends NovaApplicationServiceProvider
{
    public function boot(): void
    {
        parent::boot();

        CustomResourcesExport::use(PostsWithTagBreaking::class);
    }

    protected function routes(): void
    {
        Nova::routes()
            ->withAuthenticationRoutes()
            ->withPasswordResetRoutes()
            ->register();
    }

    protected function gate(): void
    {
        Gate::define('viewNova', function ($user) {
            return true;
        });
    }


    protected function dashboards(): array
    {
        return [
        ];
    }

    protected function resources(): void
    {
        Nova::resources([
            Post::class,
            ExportStoredFile::class,
        ]);
    }
}
