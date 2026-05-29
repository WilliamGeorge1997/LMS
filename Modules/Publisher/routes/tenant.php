<?php

use Illuminate\Support\Facades\Route;
use Modules\Publisher\Http\Controllers\PublisherController;
use Stancl\Tenancy\Middleware\InitializeTenancyBySubdomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;
use Stancl\Tenancy\Middleware\ScopeSessions;

$central = config('tenancy.central_domains')[0];

Route::domain('{tenant}.' . $central)
    ->middleware([
        InitializeTenancyBySubdomain::class,
        PreventAccessFromCentralDomains::class,
        ScopeSessions::class,
    ])
    ->prefix('admin')
    ->group(function () {
        Route::resource('publishers', PublisherController::class)->except(['show', 'edit', 'update']);
        Route::post('publishers/{publisher}', [PublisherController::class, 'update']);
        Route::patch('publishers/{publisher}/toggle-activate', [PublisherController::class, 'toggleActivate']);
    });
