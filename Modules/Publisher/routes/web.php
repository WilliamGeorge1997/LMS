<?php

use Illuminate\Support\Facades\Route;
use Modules\Publisher\Http\Controllers\PublisherController;
use Stancl\Tenancy\Middleware\InitializeTenancyBySubdomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

$central = config('tenancy.central_domains')[0];

$routes = function () {
    Route::prefix('admin')->group(function () {
        Route::resource('publishers', PublisherController::class)->except(['show', 'edit', 'update']);
        Route::post('publishers/{publisher}', [PublisherController::class, 'update'])->name('publishers.update');
        Route::patch('publishers/{publisher}/toggle-activate', [PublisherController::class, 'toggleActivate'])->name('publishers.toggle-activate');
    });
};

Route::domain('{tenant}.' . $central)->middleware([
    InitializeTenancyBySubdomain::class,
    PreventAccessFromCentralDomains::class
])->group($routes);

Route::domain($central)->group($routes);
