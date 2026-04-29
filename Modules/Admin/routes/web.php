<?php

use Illuminate\Support\Facades\Route;
use Modules\Admin\Http\Controllers\AdminController;

Route::group(['prefix' => 'admin'], function () {
    Route::get('dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');

    //Admins
    Route::resource('admins', AdminController::class)->except(['show','edit','update']);
    Route::post('admins/{admin}', [AdminController::class, 'update'])->name('admins.update');
    Route::patch('admins/{admin}/toggle-activate', [AdminController::class, 'toggleActivate'])->name('admins.toggle-activate');
});
