<?php

use Illuminate\Support\Facades\Route;
use Modules\Tenant\Http\Controllers\TenantController;

$central = config('tenancy.central_domains')[0];

Route::domain($central)
    ->middleware(['central.super_admin.tenant'])
    ->prefix('admin')
    ->group(function () {
        Route::post('tenants/set', [TenantController::class, 'set'])->name('tenants.set');

        Route::resource('tenants', TenantController::class)->except(['show']);
        Route::post('tenants/{tenant}', [TenantController::class, 'update'])->name('tenants.update');
        Route::patch('tenants/{tenant}/toggle-activate', [TenantController::class, 'toggleActivate'])->name('tenants.toggle-activate');
    });
