<?php

namespace Modules\Common\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\Common\Helpers\AjaxResponse;
use Modules\Common\Services\SettingService;

class SettingController extends Controller
{
    public function __construct(private readonly SettingService $settingService)
    {
    }

    public function index(): JsonResponse
    {
        $setting = $this->settingService->getSetting();

        return AjaxResponse::success('Settings retrieved successfully', [
            'app_version' => $setting?->app_version,
            'download_link' => $setting?->app_path,
        ]);
    }
}
