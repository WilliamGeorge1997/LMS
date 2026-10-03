<?php

use Illuminate\Support\Facades\Route;
use Modules\Country\Http\Controllers\Web\CityController;
use Modules\Country\Http\Controllers\Web\RegionController;
use Stancl\Tenancy\Middleware\InitializeTenancyBySubdomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;
use Stancl\Tenancy\Middleware\ScopeSessions;

Route::middleware([
    'web',
    InitializeTenancyBySubdomain::class,
    PreventAccessFromCentralDomains::class,
    ScopeSessions::class,
])->group(function () {
    // Public AJAX routes for registration wizard
    Route::get('/ajax/cities', [CityController::class, 'ajaxCity'])->name('web.ajax.cities');
    Route::get('/ajax/regions', [RegionController::class, 'ajaxRegion'])->name('web.ajax.regions');
});
