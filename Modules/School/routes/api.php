<?php

use Illuminate\Support\Facades\Route;
use Modules\School\Http\Controllers\Api\SchoolController;
use Stancl\Tenancy\Middleware\InitializeTenancyBySubdomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

Route::middleware([
    InitializeTenancyBySubdomain::class,
    PreventAccessFromCentralDomains::class,
])->group(function () {
    Route::get('schools', [SchoolController::class, 'index'])->name('schools');
});
