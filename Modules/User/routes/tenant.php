<?php

use Illuminate\Support\Facades\Route;
use Modules\User\Http\Controllers\Web\ProfileController;
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
    Route::get('select-type', [UserAuthController::class, 'showSelectType'])->name('select-type');
    Route::post('select-type', [UserAuthController::class, 'selectType'])->name('select-type.post');
    Route::get('select-type/{type}', [UserAuthController::class, 'selectType'])->name('select-type.with-type');

    // Auth Routes
    Route::get('login', [UserAuthController::class, 'showLogin'])->name('login');
    Route::post('login', [UserAuthController::class, 'login'])->name('login.submit');

    Route::get('register', [UserAuthController::class, 'showRegister'])->name('register');
    Route::post('register', [UserAuthController::class, 'register'])->name('register.submit');

    Route::get('verify', [UserAuthController::class, 'showVerify'])->name('verify.show');
    Route::post('verify', [UserAuthController::class, 'verify'])->name('verify.submit');
    Route::post('verify/resend', [UserAuthController::class, 'resendVerifyCode'])->name('verify.resend');

    // Forgot Password Routes
    Route::get('forgot-password', [UserAuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('forgot-password', [UserAuthController::class, 'sendResetCode'])->name('password.email');
    Route::get('forgot-password/verify', [UserAuthController::class, 'showVerifyReset'])->name('password.verify.show');
    Route::post('forgot-password/verify', [UserAuthController::class, 'verifyResetCode'])->name('password.verify.submit');
    Route::post('forgot-password/resend', [UserAuthController::class, 'resendResetCode'])->name('password.resend');
    Route::get('reset-password', [UserAuthController::class, 'showResetPassword'])->name('password.reset');
    Route::post('reset-password', [UserAuthController::class, 'updatePassword'])->name('password.update');

    Route::post('logout', [UserAuthController::class, 'logout'])->name('logout');

    // Profile Routes
    Route::get('profile', [ProfileController::class, 'show'])->name('profile');
    Route::post('profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('profile/language', [ProfileController::class, 'switchLanguage'])->name('profile.language');
    Route::post('profile/theme', [ProfileController::class, 'switchTheme'])->name('profile.theme');
});
