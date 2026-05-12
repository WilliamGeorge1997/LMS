<?php

use Illuminate\Support\Facades\Route;
use Modules\Admin\Http\Controllers\AdminAuthController;
use Modules\Admin\Http\Controllers\AdminController;

Route::group(['prefix' => 'admin'], function () {
    //Authentication
    Route::get('login', [AdminAuthController::class, 'loginForm'])->name('admin.login.form');
    Route::post('login', [AdminAuthController::class, 'login'])->name('admin.login');
    Route::post('logut', [AdminAuthController::class, 'logout'])->name('admin.logout');

    Route::get('dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');

    //Admins
    Route::resource('admins', AdminController::class)->except(['show','edit','update']);
    Route::post('admins/{admin}', [AdminController::class, 'update'])->name('admins.update');
    Route::patch('admins/{admin}/toggle-activate', [AdminController::class, 'toggleActivate'])->name('admins.toggle-activate');
});
