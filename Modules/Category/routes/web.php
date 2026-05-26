<?php

use Illuminate\Support\Facades\Route;
use Modules\Category\Http\Controllers\CategoryController;

Route::group(['prefix' => 'admin'], function () {
    //Categories
    Route::resource('categories', CategoryController::class)->except(['show', 'update']);
    Route::post('categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
    Route::patch('categories/{category}/toggle-activate', [CategoryController::class, 'toggleActivate'])->name('categories.toggle-activate');
});
