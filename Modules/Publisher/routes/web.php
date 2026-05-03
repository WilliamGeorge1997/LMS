<?php

use Illuminate\Support\Facades\Route;
use Modules\Publisher\Http\Controllers\PublisherController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('publishers', PublisherController::class)->names('publisher');
});
