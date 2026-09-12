<?php

use Illuminate\Support\Facades\Route;
use Modules\User\Http\Controllers\UserController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('users', UserController::class)->names('user');
        Route::post('users/{id}/destroy', [UserController::class, 'destroy'])->name('users.destroy');
});
