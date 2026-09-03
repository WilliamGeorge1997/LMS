<?php

use Illuminate\Support\Facades\Route;
use Modules\Country\Http\Controllers\Api\CityController;
use Modules\Country\Http\Controllers\Api\CountryController;
use Modules\Country\Http\Controllers\Api\ZoneController;
use Stancl\Tenancy\Middleware\InitializeTenancyBySubdomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

Route::middleware([
    InitializeTenancyBySubdomain::class,
    PreventAccessFromCentralDomains::class,
])->group(function () {
    Route::get('countries', [CountryController::class, 'index'])->name('countries');
    Route::get('countries/{country}/cities', [CityController::class, 'index'])->name('countries.cities');
    Route::get('cities/{city}/zones', [ZoneController::class, 'index'])->name('cities.zones');
    Route::get('cities/{city}/regions', [ZoneController::class, 'index'])->name('cities.regions');
});
