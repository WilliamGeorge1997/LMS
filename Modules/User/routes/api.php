<?php

use Illuminate\Support\Facades\Route;
use Modules\User\Http\Controllers\UserAuthController;
use Modules\User\Http\Controllers\UserController;

Route::prefix('auth')->group(function () {
    Route::post('register', [UserAuthController::class, 'register']);
    Route::post('login', [UserAuthController::class, 'login']);
    Route::post('logout', [UserAuthController::class, 'logout']);
});

Route::apiResource('users', UserController::class)->names('user');

