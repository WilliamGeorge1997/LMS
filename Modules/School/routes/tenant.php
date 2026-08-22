<?php

use Illuminate\Support\Facades\Route;
use Modules\School\Http\Controllers\SchoolController;
use Stancl\Tenancy\Middleware\InitializeTenancyBySubdomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;
use Stancl\Tenancy\Middleware\ScopeSessions;

$central = config('tenancy.central_domains')[0];

// Route::domain('{tenant}.' . $central)
Route::middleware([
        InitializeTenancyBySubdomain::class,
        PreventAccessFromCentralDomains::class,
        ScopeSessions::class,
    ])
    ->prefix('admin')
    ->group(function () {
        Route::get('schools/ajax_city', [SchoolController::class, 'ajaxCity']);
        Route::get('schools/ajax_region', [SchoolController::class, 'ajaxRegion']);

        Route::resource('schools', SchoolController::class)->except(['show', 'update']);
        Route::get('schools/{school}/edit', [SchoolController::class, 'edit']);
        Route::post('schools/{school}', [SchoolController::class, 'update']);
        Route::patch('schools/{school}/toggle-activate', [SchoolController::class, 'toggleActivate']);
    });
