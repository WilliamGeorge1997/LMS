<?php

use Illuminate\Support\Facades\Route;
use Modules\Tenant\Http\Controllers\TenantController;

Route::group(['prefix' => 'admin'], function () {
    Route::post('tenants/initialize', [TenantController::class, 'initialize'])->name('tenants.initialize');

    Route::resource('tenants', TenantController::class)->except(['show']);
    Route::post('tenants/{tenant}', [TenantController::class, 'update'])->name('tenants.update');
    Route::patch('tenants/{tenant}/toggle-activate', [TenantController::class, 'toggleActivate'])->name('tenants.toggle-activate');
});
