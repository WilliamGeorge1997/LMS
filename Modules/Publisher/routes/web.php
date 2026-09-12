<?php

use Illuminate\Support\Facades\Route;
use Modules\Publisher\Http\Controllers\PublisherController;

$central = config('tenancy.central_domains')[0];

Route::domain($central)
    ->middleware(['central.super_admin.tenant'])
    ->prefix('admin')
    ->group(function () {
        Route::resource('publishers', PublisherController::class)->except(['show', 'update']);
        Route::post('publishers/{publisher}', [PublisherController::class, 'update'])->name('publishers.update');
        Route::patch('publishers/{publisher}/toggle-activate', [PublisherController::class, 'toggleActivate'])->name('publishers.toggle-activate');
    });
