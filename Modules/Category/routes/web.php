<?php

use Illuminate\Support\Facades\Route;
use Modules\Category\Http\Controllers\CategoryController;

$central = config('tenancy.central_domains')[0];

Route::domain($central)
    ->middleware(['central.super_admin.tenant'])
    ->prefix('admin')
    ->group(function () {
        Route::get('categories/ajax_category', [CategoryController::class, 'ajaxCategory']);
        Route::resource('categories', CategoryController::class)->except(['show', 'update']);
        Route::post('categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
        Route::patch('categories/{category}/toggle-activate', [CategoryController::class, 'toggleActivate'])->name('categories.toggle-activate');
    });
