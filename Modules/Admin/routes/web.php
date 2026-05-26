<?php

use Illuminate\Support\Facades\Route;
use Modules\Admin\Http\Controllers\AdminAuthController;
use Modules\Admin\Http\Controllers\AdminController;

$central = config('tenancy.central_domains')[0];

Route::domain($central)
    ->middleware(['central.super_admin.tenant'])
    ->prefix('admin')
    ->group(function () {
        Route::get('login', [AdminAuthController::class, 'loginForm'])->name('admin.login.form');
        Route::post('login', [AdminAuthController::class, 'login'])->name('admin.login');
        Route::post('logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

        Route::get('dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');

        Route::resource('admins', AdminController::class)->except(['show', 'update']);
        Route::post('admins/{admin}', [AdminController::class, 'update'])->name('admins.update');
        Route::patch('admins/{admin}/toggle-activate', [AdminController::class, 'toggleActivate'])->name('admins.toggle-activate');
    });
