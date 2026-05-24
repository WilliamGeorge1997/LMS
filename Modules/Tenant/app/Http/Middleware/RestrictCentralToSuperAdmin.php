<?php

namespace Modules\Tenant\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Modules\Admin\Enums\Role;
use Symfony\Component\HttpFoundation\Response;

class RestrictCentralToSuperAdmin
{
    /**
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! in_array($request->getHost(), config('tenancy.central_domains'), true)) {
            return $next($request);
        }

        $admin = auth('admin')->user();

        if ($admin && ! $admin->hasRole(Role::SUPER_ADMIN->value)) {
            abort(403);
        }

        return $next($request);
    }
}
