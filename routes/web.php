<?php


use Illuminate\Support\Facades\Route;
use NovaResourceDynamicExport\DynamicExport;

Route::group(DynamicExport::routeConfiguration(), function () {
    Route::get(
        '{file}',
        \NovaResourceDynamicExport\Http\Controllers\DownloadExportController::class
    )
        ->where('file', '^[\.a-zA-Z0-9-_\/]+$')
        ->name(config('nova-resource-dynamic-export.defaults.download_route'));
});
