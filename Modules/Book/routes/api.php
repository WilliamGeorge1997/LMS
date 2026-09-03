<?php

use Illuminate\Support\Facades\Route;
use Modules\Book\Http\Controllers\Api\BookCodeController;
use Modules\Book\Http\Controllers\Api\BookController;

Route::get('my-books', [BookController::class, 'myBooks']);
Route::post('book-codes/redeem', [BookCodeController::class, 'redeem']);
