<?php

use Illuminate\Support\Facades\Route;
use Modules\Book\Http\Controllers\BookController;
use Modules\Book\Http\Controllers\BookCodeController;
use Stancl\Tenancy\Middleware\InitializeTenancyBySubdomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;
use Stancl\Tenancy\Middleware\ScopeSessions;

$central = config('tenancy.central_domains')[0];

// Route::domain('{tenant}.' . $central)
Route::middleware([
        InitializeTenancyBySubdomain::class,
        PreventAccessFromCentralDomains::class,
        ScopeSessions::class,
    ])
    ->prefix('admin')
    ->group(function () {
        Route::resource('books', BookController::class)->except(['show', 'update', 'destroy']);
        Route::post('books/{book}/destroy', [BookController::class, 'destroy'])->name('books.destroy');
        Route::post('books/{book}', [BookController::class, 'update']);
        Route::post('books/{book}/toggle-activate', [BookController::class, 'toggleActivate']);

        Route::get('book-codes/export', [BookCodeController::class, 'export'])->name('tenant.book-codes.export');

        Route::resource('book-codes', BookCodeController::class)->only(['index', 'store']);
        Route::post('book-codes/{book_code}/destroy', [BookCodeController::class, 'destroy'])->name('book-codes.destroy');
        Route::post('book-codes/{book_code}/toggle-activate', [BookCodeController::class, 'toggleActivate'])
            ->name('book-codes.toggle-activate');
    });
