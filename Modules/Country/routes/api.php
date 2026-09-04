<?php

use Illuminate\Support\Facades\Route;
use Modules\Country\Http\Controllers\Api\CityController;
use Modules\Country\Http\Controllers\Api\CountryController;
use Modules\Country\Http\Controllers\Api\RegionController;
use Stancl\Tenancy\Middleware\InitializeTenancyBySubdomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

Route::middleware([
    InitializeTenancyBySubdomain::class,
    PreventAccessFromCentralDomains::class,
])->group(function () {
    Route::get('countries', [CountryController::class, 'index'])->name('countries');
    Route::get('countries/{country}/cities', [CityController::class, 'index'])->name('countries.cities');
    Route::get('cities/{city}/regions', [RegionController::class, 'index'])->name('cities.regions');
});
