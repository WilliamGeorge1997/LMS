<?php

use Illuminate\Support\Facades\Route;
use Modules\Publisher\Http\Controllers\PublisherController;

Route::group(['prefix' => 'admin'], function () {
    //Publishers
    Route::resource('publishers', PublisherController::class)->except(['show','edit','update']);
    Route::post('publishers/{publisher}', [PublisherController::class, 'update'])->name('publishers.update');
    Route::patch('publishers/{publisher}/toggle-activate', [PublisherController::class, 'toggleActivate'])->name('publishers.toggle-activate');
});
