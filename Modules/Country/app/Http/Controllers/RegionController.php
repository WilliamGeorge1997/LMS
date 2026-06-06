<?php

namespace Modules\Country\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Modules\Admin\Enums\Role;
use Modules\Common\Helpers\AjaxResponse;
use Modules\Country\DTOs\RegionDto;
use Modules\Country\Http\Requests\RegionStoreRequest;
use Modules\Country\Http\Requests\RegionUpdateRequest;
use Modules\Country\Models\Region;
use Modules\Country\Services\RegionService;
use Modules\Country\ViewModel\CountryViewModel;

class RegionController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            'auth:admin',
            'role:' . Role::SUPER_ADMIN->value,
            'set.locale',
        ];
    }

    public function __construct(private readonly RegionService $regionService)
    {
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->regionService->dataTable();
        }

        $viewModel = new CountryViewModel;

        return view('country::regions.index', compact('viewModel'));
    }

    public function store(RegionStoreRequest $request): JsonResponse
    {
        $dto = RegionDto::fromRequest($request);
        $region = $this->regionService->save($dto);

        return AjaxResponse::success(__('country::messages.region_created_successfully'), $region);
    }

    public function edit(Region $region): string
    {
        $viewModel = new CountryViewModel;

        return view('country::regions.partials.edit', [
            'region' => $region->load('city.country'),
            'viewModel' => $viewModel,
        ])->render();
    }

    public function update(RegionUpdateRequest $request, Region $region): JsonResponse
    {
        $dto = RegionDto::fromRequest($request);
        $region = $this->regionService->update($region, $dto);

        return AjaxResponse::success(__('country::messages.region_updated_successfully'), $region);
    }

    public function destroy(Region $region): JsonResponse
    {
        $status = $this->regionService->delete($region);

        return $status
            ? AjaxResponse::success(__('country::messages.region_deleted_successfully'))
            : AjaxResponse::error();
    }

    public function toggleActivate(Region $region): JsonResponse
    {
        $region = $this->regionService->toggleActivate($region);
        $message = $region->is_active
            ? __('country::messages.region_activated_successfully')
            : __('country::messages.region_deactivated_successfully');

        return AjaxResponse::success($message, $region);
    }
}
