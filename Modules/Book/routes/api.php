<?php

use Illuminate\Support\Facades\Route;
use Modules\Book\Http\Controllers\Api\BookController;

Route::get('my-books', [BookController::class, 'myBooks'])->name('my-books');
