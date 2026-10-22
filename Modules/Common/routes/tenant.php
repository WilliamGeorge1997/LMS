<?php

use Illuminate\Support\Facades\Route;
use Modules\Common\Http\Controllers\Web\CommonController;
use Stancl\Tenancy\Middleware\InitializeTenancyBySubdomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;
use Stancl\Tenancy\Middleware\ScopeSessions;

Route::middleware([
    'web',
    InitializeTenancyBySubdomain::class,
    PreventAccessFromCentralDomains::class,
    ScopeSessions::class,
])->group(function () {
    Route::get('/', [CommonController::class, 'index'])->name('welcome');
});
