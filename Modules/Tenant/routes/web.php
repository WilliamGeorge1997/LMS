<?php

use Illuminate\Support\Facades\Route;
use Modules\Tenant\Http\Controllers\TenantController;

Route::group(['prefix' => 'admin'], function () {

    Route::resource('tenants', TenantController::class);
    Route::post('tenants/{tenant}', [TenantController::class, 'update'])->name('tenants.update');
    Route::patch('tenants/{tenant}/toggle-activate', [TenantController::class, 'toggleActivate'])->name('tenants.toggle-activate');
});
