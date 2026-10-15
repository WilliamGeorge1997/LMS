<?php

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Stancl\Tenancy\Contracts\TenantCouldNotBeIdentifiedException;
use Stancl\Tenancy\Exceptions\NotASubdomainException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class ExceptionHandler
{
    public static function handle(Exceptions $exceptions): void
    {
        $exceptions->render(function (Throwable $e, Request $request) {
            $isApi = $request->is('api/*') || $request->wantsJson();

            // 1. Tenancy / Subdomain Exceptions
            if ($e instanceof NotASubdomainException || $e instanceof TenantCouldNotBeIdentifiedException) {
                if ($isApi) {
                    return apiResponse(false, 'Invalid organization link or subdomain.', null, 'invalid_tenant');
                }

                return response()->view('errors.not-found', [
                    'title' => __('common::messages.not_found'),
                    'description' => __('common::messages.invalid_subdomain'),
                ], 404);
            }

            // 2. API Exceptions
            if ($isApi) {
                return match (true) {
                    $e instanceof AuthenticationException => apiResponse(false, 'Unauthenticated.', null, 'unauthorized'),

                    $e instanceof NotFoundHttpException, $e instanceof ModelNotFoundException => apiResponse(false, 'Resource not found.', null, 'not_found'),

                    $e instanceof AccessDeniedHttpException, $e instanceof AuthorizationException => apiResponse(false, 'Action unauthorized.', null, 'forbidden'),

                    $e instanceof MethodNotAllowedHttpException => apiResponse(false, 'Method not allowed.', null, 'method_not_allowed'),

                    $e instanceof ValidationException => apiResponse(false, 'Validation errors', $e->errors(), 'validation_error'),

                    default => apiResponse(
                        false,
                        config('app.debug') ? $e->getMessage() : 'Server Error.',
                        config('app.debug') ? ['trace' => $e->getTrace()] : null,
                        'server_error'
                    ),
                };
            }
        });
    }
}
