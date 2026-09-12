<?php

use Illuminate\Support\Facades\Route;
use Modules\User\Http\Controllers\Api\UserAuthController;
use Modules\User\Http\Controllers\UserController;
use Stancl\Tenancy\Middleware\InitializeTenancyBySubdomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;


Route::middleware([
    InitializeTenancyBySubdomain::class,
    PreventAccessFromCentralDomains::class,
])->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('register', [UserAuthController::class, 'register']);
        Route::post('login', [UserAuthController::class, 'login']);
        Route::post('verify', [UserAuthController::class, 'verify']);
        Route::post('logout', [UserAuthController::class, 'logout']);

        Route::post('forget-password', [UserAuthController::class, 'forgetPassword']);
        Route::post('verify-forget-password', [UserAuthController::class, 'verifyForgetPassword']);
        Route::post('new-password', [UserAuthController::class, 'newPassword']);
    });
    Route::post('edit-profile', [UserAuthController::class, 'editProfile']);

    Route::apiResource('users', UserController::class)->names('user');
});
