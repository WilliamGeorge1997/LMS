<?php

use Illuminate\Support\Facades\Route;
use Modules\Book\Http\Controllers\BookController;

$central = config('tenancy.central_domains')[0];

Route::domain($central)
    ->middleware(['central.super_admin.tenant'])
    ->prefix('admin')
    ->group(function () {
        Route::resource('books', BookController::class)->except(['show', 'update']);
        Route::post('books/{book}', [BookController::class, 'update'])->name('books.update');
        Route::patch('books/{book}/toggle-activate', [BookController::class, 'toggleActivate'])->name('books.toggle-activate');
    });
