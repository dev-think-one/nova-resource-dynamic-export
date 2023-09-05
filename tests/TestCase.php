<?php

namespace NovaResourceDynamicExport\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Laravel\Nova\NovaCoreServiceProvider;
use NovaResourceDynamicExport\Tests\Fixtures\NovaServiceProvider;
use Orchestra\Testbench\Database\MigrateProcessor;

class TestCase extends \Orchestra\Testbench\TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake();
        Storage::fake('exports');

        Artisan::call('nova:publish');
    }

    protected function getPackageProviders($app): array
    {
        return [
            \Inertia\ServiceProvider::class,
            NovaCoreServiceProvider::class,
            NovaServiceProvider::class,
            \Maatwebsite\Excel\ExcelServiceProvider::class,
            \Maatwebsite\LaravelNovaExcel\LaravelNovaExcelServiceProvider::class,
            \NovaResourceDynamicExport\ServiceProvider::class,
        ];
    }

    protected function defineDatabaseMigrations(): void
    {
        $this->loadLaravelMigrations();

        $migrator = new MigrateProcessor($this, [
            '--path'     => __DIR__ . '/Fixtures/migrations',
            '--realpath' => true,
        ]);
        $migrator->up();
    }

    protected function getEnvironmentSetUp($app): void
    {
        // Setup default database to use sqlite :memory:
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);

        $app['config']->set('filesystems.disks', array_merge(
            $app['config']->get('filesystems.disks'),
            [
                'exports' => [
                    'driver' => 'local',
                    'root'   => storage_path('app/exports'),
                ],
            ]
        ));

        // $app['config']->set('nova-resource-dynamic-export.some_key', 'some_value');
    }
}
