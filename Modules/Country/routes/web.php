<?php

use Illuminate\Support\Facades\Route;
use Modules\Country\Http\Controllers\CityController;
use Modules\Country\Http\Controllers\CountryController;
use Modules\Country\Http\Controllers\RegionController;

Route::group(['prefix' => 'admin'], function () {
    //Ajax
    // Route::get('countries/select-options', [CountryController::class, 'selectOptions'])->name('countries.select-options');
    // Route::get('cities/select-options', [CityController::class, 'selectOptions'])->name('cities.select-options');

    //Countries
    Route::resource('countries', CountryController::class)->except(['show', 'edit', 'update']);
    Route::post('countries/{country}', [CountryController::class, 'update'])->name('countries.update');
    Route::patch('countries/{country}/toggle-activate', [CountryController::class, 'toggleActivate'])->name('countries.toggle-activate');

    //Cities
    Route::resource('cities', CityController::class)->except(['show', 'edit', 'update']);
    Route::post('cities/{city}', [CityController::class, 'update'])->name('cities.update');
    Route::patch('cities/{city}/toggle-activate', [CityController::class, 'toggleActivate'])->name('cities.toggle-activate');

    //Regions
    Route::resource('regions', RegionController::class)->except(['show', 'edit', 'update']);
    Route::post('regions/{region}', [RegionController::class, 'update'])->name('regions.update');
    Route::patch('regions/{region}/toggle-activate', [RegionController::class, 'toggleActivate'])->name('regions.toggle-activate');
});
