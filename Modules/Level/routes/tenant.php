<?php

use Illuminate\Support\Facades\Route;
use Modules\Level\Http\Controllers\LevelController;
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
        Route::get('levels/ajax_level', [LevelController::class, 'ajaxLevel']);
        Route::resource('levels', LevelController::class)->except(['show', 'update']);
        Route::post('levels/{level}', [LevelController::class, 'update']);
        Route::patch('levels/{level}/toggle-activate', [LevelController::class, 'toggleActivate']);
    });
