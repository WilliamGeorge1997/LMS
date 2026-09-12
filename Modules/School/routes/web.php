<?php

use Illuminate\Support\Facades\Route;
use Modules\School\Http\Controllers\SchoolController;

$central = config('tenancy.central_domains')[0];

Route::domain($central)
    ->middleware(['central.super_admin.tenant'])
    ->prefix('admin')
    ->group(function () {
        Route::get('schools/ajax_city', [SchoolController::class, 'ajaxCity'])->name('schools.ajax_city');
        Route::get('schools/ajax_region', [SchoolController::class, 'ajaxRegion'])->name('schools.ajax_region');

        Route::resource('schools', SchoolController::class)->except(['show', 'update']);
        Route::post('schools/{school}', [SchoolController::class, 'update'])->name('schools.update');
        Route::patch('schools/{school}/toggle-activate', [SchoolController::class, 'toggleActivate'])->name('schools.toggle-activate');
    });
