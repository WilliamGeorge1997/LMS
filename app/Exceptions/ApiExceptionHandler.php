<?php

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class ApiExceptionHandler
{
    public static function handle(Exceptions $exceptions): void
    {
        $exceptions->render(function (Throwable $e, Request $request) {
            if ($request->is('api/*') || $request->wantsJson()) {
                
                if ($e instanceof AuthenticationException) {
                    return apiResponse(false, 'Unauthenticated.', null, 'unauthorized');
                }

                if ($e instanceof NotFoundHttpException || $e instanceof ModelNotFoundException) {
                    return apiResponse(false, 'Resource not found.', null, 'not_found');
                }

                if ($e instanceof AccessDeniedHttpException || $e instanceof AuthorizationException) {
                    return apiResponse(false, 'Action unauthorized.', null, 'forbidden');
                }

                if ($e instanceof MethodNotAllowedHttpException) {
                    return apiResponse(false, 'Method not allowed.', null, 'method_not_allowed');
                }

                if ($e instanceof ValidationException) {
                    return apiResponse(false, 'Validation errors', $e->errors(), 'validation_error');
                }

                // Default fallback for any other unhandled exception in API
                // Depending on the APP_DEBUG setting, we might not want to hide the real error message locally
                $message = config('app.debug') ? $e->getMessage() : 'Server Error.';
                return apiResponse(false, $message, config('app.debug') ? ['trace' => $e->getTrace()] : null, 'server_error');
            }
        });
    }
}
