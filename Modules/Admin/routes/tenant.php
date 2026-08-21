<?php

use Illuminate\Support\Facades\Route;
use Modules\Admin\Http\Controllers\AdminAuthController;
use Modules\Admin\Http\Controllers\AdminController;
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
        Route::get('login', [AdminAuthController::class, 'loginForm']);
        Route::post('login', [AdminAuthController::class, 'login']);
        Route::post('logout', [AdminAuthController::class, 'logout']);

        Route::get('dashboard', [AdminController::class, 'dashboard']);

        Route::resource('admins', AdminController::class)->except(['show', 'update']);
        Route::post('admins/{admin}', [AdminController::class, 'update']);
        Route::patch('admins/{admin}/toggle-activate', [AdminController::class, 'toggleActivate']);
    });
