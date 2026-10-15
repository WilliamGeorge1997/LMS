<?php

use App\Exceptions\ExceptionHandler;
use App\Http\Middleware\SetLocale;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Modules\Tenant\Http\Middleware\EnsureCentralSuperAdminWithTenant;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            SetLocale::class,
        ]);

        $middleware->redirectGuestsTo(function ($request) {
            if ($request->is('admin') || $request->is('admin/*')) {
                return '/admin/login';
            }

            return '/';
        });

        $middleware->redirectUsersTo(function ($request) {
            if (auth('admin')->check()) {
                return '/admin/dashboard';
            }

            return '/my-books';
        });
        $middleware->alias([
            'set.locale' => SetLocale::class,
            'role' => RoleMiddleware::class,
            'permission' => PermissionMiddleware::class,
            'central.super_admin.tenant' => EnsureCentralSuperAdminWithTenant::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        ExceptionHandler::handle($exceptions);
    })->create();
