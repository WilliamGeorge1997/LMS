<?php

use Illuminate\Support\Facades\Route;

$central = config('tenancy.central_domains')[0];

Route::domain($central)->group(function () {
    Route::get('/', function () {
        return redirect()->route('admin.dashboard');
    });
});
