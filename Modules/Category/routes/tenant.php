<?php

use Illuminate\Support\Facades\Route;
use Modules\Category\Http\Controllers\CategoryController;
use Stancl\Tenancy\Middleware\InitializeTenancyBySubdomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;
use Stancl\Tenancy\Middleware\ScopeSessions;

$central = config('tenancy.central_domains')[0];

Route::domain('{tenant}.'.$central)
    ->middleware([
        InitializeTenancyBySubdomain::class,
        PreventAccessFromCentralDomains::class,
        ScopeSessions::class,
    ])
    ->prefix('admin')
    ->group(function () {
        Route::resource('categories', CategoryController::class)->except(['show', 'edit', 'update']);
        Route::post('categories/{category}', [CategoryController::class, 'update']);
        Route::patch('categories/{category}/toggle-activate', [CategoryController::class, 'toggleActivate']);
    });
