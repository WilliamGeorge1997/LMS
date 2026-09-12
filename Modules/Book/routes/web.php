<?php

use Illuminate\Support\Facades\Route;
use Modules\Book\Http\Controllers\BookCodeController;
use Modules\Book\Http\Controllers\BookController;

$central = config('tenancy.central_domains')[0];

Route::domain($central)
    ->middleware(['central.super_admin.tenant'])
    ->prefix('admin')
    ->group(function () {
        Route::resource('books', BookController::class)->except(['show', 'update', 'destroy']);
        Route::post('books/{book}/destroy', [BookController::class, 'destroy'])->name('books.destroy');
        Route::post('books/{book}', [BookController::class, 'update'])->name('books.update');
        Route::post('books/{book}/toggle-activate', [BookController::class, 'toggleActivate'])->name('books.toggle-activate');
        Route::post('books/{book}/upload-chunk', [BookController::class, 'uploadChunk'])->name('books.upload-chunk');

        Route::get('book-codes/export', [BookCodeController::class, 'export'])->name('book-codes.export');

        Route::resource('book-codes', BookCodeController::class)->only(['index', 'store']);
        Route::post('book-codes/{book_code}/destroy', [BookCodeController::class, 'destroy'])->name('book-codes.destroy');
        Route::post('book-codes/{book_code}/toggle-activate', [BookCodeController::class, 'toggleActivate'])
            ->name('book-codes.toggle-activate');
    });
