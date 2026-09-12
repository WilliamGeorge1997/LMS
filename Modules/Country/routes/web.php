<?php

use Illuminate\Support\Facades\Route;
use Modules\Country\Http\Controllers\CityController;
use Modules\Country\Http\Controllers\CountryController;
use Modules\Country\Http\Controllers\RegionController;

$central = config('tenancy.central_domains')[0];

Route::domain($central)
    ->middleware(['central.super_admin.tenant'])
    ->prefix('admin')
    ->group(function () {
        Route::get('cities/ajax_city', [CityController::class, 'ajaxCity'])->name('cities.ajax_city');

        Route::resource('countries', CountryController::class)->except(['show', 'update']);
        Route::post('countries/{country}', [CountryController::class, 'update'])->name('countries.update');
        Route::patch('countries/{country}/toggle-activate', [CountryController::class, 'toggleActivate'])->name('countries.toggle-activate');

        Route::resource('cities', CityController::class)->except(['show', 'update']);
        Route::post('cities/{city}', [CityController::class, 'update'])->name('cities.update');
        Route::patch('cities/{city}/toggle-activate', [CityController::class, 'toggleActivate'])->name('cities.toggle-activate');

        Route::resource('regions', RegionController::class)->except(['show', 'update']);
        Route::post('regions/{region}', [RegionController::class, 'update'])->name('regions.update');
        Route::patch('regions/{region}/toggle-activate', [RegionController::class, 'toggleActivate'])->name('regions.toggle-activate');
    });
