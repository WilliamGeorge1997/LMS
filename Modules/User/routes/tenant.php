<?php

use Illuminate\Support\Facades\Route;
use Modules\User\Http\Controllers\Web\UserAuthController;
use Stancl\Tenancy\Middleware\InitializeTenancyBySubdomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;
use Stancl\Tenancy\Middleware\ScopeSessions;

Route::middleware([
    'web',
    InitializeTenancyBySubdomain::class,
    PreventAccessFromCentralDomains::class,
    ScopeSessions::class,
])->group(function () {
    // Auth Routes
    Route::get('login', [UserAuthController::class, 'showLogin'])->name('web.login');
    Route::post('login', [UserAuthController::class, 'login'])->name('web.login.submit');

    Route::get('register', [UserAuthController::class, 'showRegister'])->name('web.register');
    Route::post('register', [UserAuthController::class, 'register'])->name('web.register.submit');

    Route::post('logout', [UserAuthController::class, 'logout'])->name('web.logout');
});
