<?php

use Illuminate\Support\Facades\Route;
use Modules\Level\Http\Controllers\LevelController;

$central = config('tenancy.central_domains')[0];

Route::domain($central)
    ->middleware(['central.super_admin.tenant'])
    ->prefix('admin')
    ->group(function () {
        Route::get('levels/ajax_level', [LevelController::class, 'ajaxLevel']);
        Route::resource('levels', LevelController::class)->except(['show', 'update']);
        Route::post('levels/{level}', [LevelController::class, 'update'])->name('levels.update');
        Route::patch('levels/{level}/toggle-activate', [LevelController::class, 'toggleActivate'])->name('levels.toggle-activate');
    });
