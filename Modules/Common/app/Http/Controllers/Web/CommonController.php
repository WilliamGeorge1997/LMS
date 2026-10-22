<?php

namespace Modules\Common\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Modules\Common\Services\SettingService;

class CommonController extends Controller
{
    public function __construct(
        protected readonly SettingService $settingService
    ) {}

    public function index(): View
    {
        $tenantId = tenant('id');
        $setting = $this->settingService->findByTenantId($tenantId);

        return view('common::web.landing', [
            'appVersion' => $setting?->app_version,
            'appPath' => $setting?->app_path,
        ]);
    }
}
