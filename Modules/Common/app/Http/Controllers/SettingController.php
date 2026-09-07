<?php

namespace Modules\Common\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controllers\HasMiddleware;
use Modules\Admin\Enums\Role;
use Modules\Common\DTOs\SettingDto;
use Modules\Common\Helpers\AjaxResponse;
use Modules\Common\Http\Requests\SettingRequest;
use Modules\Common\Services\SettingService;

class SettingController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            'auth:admin',
            'role:' . Role::SUPER_ADMIN->value,
            'set.locale',
        ];
    }
    public function __construct(
        private readonly SettingService $settingService
    ) {}

    /**
     * Display the setting form.
     */
    public function index()
    {
        $tenantId = tenant('id') ?? session('admin_tenant_id');

        $setting = $this->settingService->findByTenantId($tenantId);

        return view('common::settings.index', compact('setting', 'tenantId'));
    }

    /**
     * Update or create the setting.
     */
    public function update(SettingRequest $request): JsonResponse
    {
        $tenantId = tenant('id') ?? session('admin_tenant_id');

        if (! $tenantId) {
            return AjaxResponse::error(__('common::messages.tenant_required'));
        }

        $dto = SettingDto::fromRequest($request);
        $setting = $this->settingService->updateOrCreate($tenantId, $dto);

        return AjaxResponse::success(__('common::messages.updated_successfully'), $setting);
    }
}
