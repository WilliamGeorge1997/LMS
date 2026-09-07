<?php

use Illuminate\Support\Facades\Route;
use Modules\Common\Http\Controllers\Api\SettingController;
use Stancl\Tenancy\Middleware\InitializeTenancyBySubdomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

Route::middleware([
    InitializeTenancyBySubdomain::class,
    PreventAccessFromCentralDomains::class,
])->group(function () {
    Route::get('settings', [SettingController::class, 'index'])->name('api.settings.index');
});
