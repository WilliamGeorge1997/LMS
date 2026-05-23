<?php

namespace Modules\Tenant\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Modules\Admin\Enums\Role;
use Stancl\Tenancy\Exceptions\TenantCouldNotBeIdentifiedById;
use Symfony\Component\HttpFoundation\Response;

class InitializeAdminTenant
{
    /**
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!in_array($request->getHost(), config('tenancy.central_domains'), true)) {
            return $next($request);
        }

        $admin = auth('admin')->user();

        if (!$admin?->hasRole(Role::SUPER_ADMIN->value)) {
            return $next($request);
        }

        $tenantId = session('admin_tenant_id');

        if (!$tenantId) {
            return $next($request);
        }

        try {
            tenancy()->initialize($tenantId);
        } catch (TenantCouldNotBeIdentifiedById) {
            session()->forget('admin_tenant_id');
        }

        return $next($request);
    }
}
