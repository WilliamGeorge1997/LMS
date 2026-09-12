<?php

use Illuminate\Support\Facades\Route;
use Modules\Book\Http\Controllers\Api\BookCodeController;
use Modules\Book\Http\Controllers\Api\BookController;
use Stancl\Tenancy\Middleware\InitializeTenancyBySubdomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;


Route::middleware([
    InitializeTenancyBySubdomain::class,
    PreventAccessFromCentralDomains::class,
])->group(function () {
    Route::get('my-books', [BookController::class, 'myBooks']);
    Route::post('book-codes/redeem', [BookCodeController::class, 'redeem']);
});
