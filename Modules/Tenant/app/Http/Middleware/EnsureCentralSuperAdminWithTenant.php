<?php

namespace Modules\Tenant\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Modules\Admin\Enums\Role;
use Modules\Admin\Models\Admin;
use Stancl\Tenancy\Exceptions\TenantCouldNotBeIdentifiedById;
use Symfony\Component\HttpFoundation\Response;

class EnsureCentralSuperAdminWithTenant
{
    /**
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! in_array($request->getHost(), config('tenancy.central_domains'), true)) {
            return $next($request);
        }

        /** @var Admin $admin */
        $admin = auth('admin')->user();

        if ($admin && ! $admin->hasRole(Role::SUPER_ADMIN->value)) {
            abort(403);
        }

        if ($admin?->hasRole(Role::SUPER_ADMIN->value)) {
            $tenantId = session('admin_tenant_id');

            if ($tenantId) {
                try {
                    tenancy()->initialize($tenantId);
                } catch (TenantCouldNotBeIdentifiedById) {
                    session()->forget('admin_tenant_id');
                }
            }
        }

        return $next($request);
    }
}
