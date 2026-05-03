<?php

use Illuminate\Support\Facades\Route;
use Modules\Publisher\Http\Controllers\PublisherController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('publishers', PublisherController::class)->names('publisher');
});
