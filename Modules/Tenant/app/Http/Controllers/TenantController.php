<?php

namespace Modules\Tenant\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Modules\Admin\Enums\Role;
use Modules\Common\Helpers\ApiResponse;
use Modules\Tenant\DTOs\TenantDto;
use Modules\Tenant\Http\Requests\TenantRequest;
use Modules\Tenant\Models\Tenant;
use Modules\Tenant\Services\TenantService;

class TenantController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            'auth:admin',
            'role:' . Role::SUPER_ADMIN->value,
            'set.locale',
        ];
    }

    public function __construct(private readonly TenantService $tenantService)
    {
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->tenantService->dataTable();
        }

        return view('tenant::tenants.index');
    }

    public function store(TenantRequest $request): JsonResponse
    {
        $dto = TenantDto::fromRequest($request);
        $tenant = $this->tenantService->save($dto);

        return ApiResponse::success(__('tenant::messages.created_successfully'), $tenant);
    }

    public function edit(Tenant $tenant): string
    {
        return view('tenant::tenants.partials.edit', ['tenant' => $tenant->load('domains')])->render();
    }

    public function update(TenantRequest $request, Tenant $tenant): JsonResponse
    {
        $dto = TenantDto::fromRequest($request);
        $tenant = $this->tenantService->update($tenant, $dto);

        return ApiResponse::success(__('tenant::messages.updated_successfully'), $tenant);
    }

    public function destroy(Tenant $tenant): JsonResponse
    {
        $status = $this->tenantService->delete($tenant);

        return $status
            ? ApiResponse::success(__('tenant::messages.deleted_successfully'))
            : ApiResponse::error();
    }

    public function toggleActivate(Tenant $tenant): JsonResponse
    {
        $tenant = $this->tenantService->toggleActivate($tenant);
        $message = $tenant->is_active
            ? __('tenant::messages.activated_successfully')
            : __('tenant::messages.deactivated_successfully');

        return ApiResponse::success($message, $tenant);
    }

    public function initialize(Request $request): RedirectResponse
    {
        if ($request->filled('tenant_id')) {
            session(['admin_tenant_id' => $request->input('tenant_id')]);
        } else {
            session()->forget('admin_tenant_id');
        }

        return back();
    }
}
