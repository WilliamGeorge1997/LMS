<?php

namespace Modules\School\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Modules\Admin\Enums\Role;
use Modules\Common\Helpers\AjaxResponse;
use Modules\Country\Services\CityService;
use Modules\Country\Services\RegionService;
use Modules\School\DTOs\SchoolDto;
use Modules\School\Http\Requests\SchoolStoreRequest;
use Modules\School\Http\Requests\SchoolUpdateRequest;
use Modules\School\Models\School;
use Modules\School\Services\SchoolService;
use Modules\School\ViewModel\SchoolViewModel;

class SchoolController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            'auth:admin',
            'role:' . Role::SUPER_ADMIN->value . '|' . Role::MANAGER->value,
            'set.locale',
        ];
    }

    public function __construct(
        private readonly SchoolService $schoolService,
        private readonly CityService $cityService,
        private readonly RegionService $regionService,
    ) {
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->schoolService->dataTable();
        }

        $viewModel = new SchoolViewModel;

        return view('school::schools.index', compact('viewModel'));
    }

    public function store(SchoolStoreRequest $request): JsonResponse
    {
        $dto = SchoolDto::fromRequest($request);
        $school = $this->schoolService->save($dto);

        return AjaxResponse::success(__('school::messages.created_successfully'), $school);
    }

    public function edit(School $school): string
    {
        $viewModel = new SchoolViewModel;

        return view('school::schools.partials.edit', [
            'school' => $school->load(['country', 'city', 'region', 'tenant']),
            'viewModel' => $viewModel,
        ])->render();
    }

    public function update(SchoolUpdateRequest $request, School $school): JsonResponse
    {
        $dto = SchoolDto::fromRequest($request);
        $school = $this->schoolService->update($school, $dto);

        return AjaxResponse::success(__('school::messages.updated_successfully'), $school);
    }

    public function destroy(School $school): JsonResponse
    {
        $status = $this->schoolService->delete($school);

        return $status
            ? AjaxResponse::success(__('school::messages.deleted_successfully'))
            : AjaxResponse::error();
    }

    public function toggleActivate(School $school): JsonResponse
    {
        $school = $this->schoolService->toggleActivate($school);
        $message = $school->is_active
            ? __('school::messages.activated_successfully')
            : __('school::messages.deactivated_successfully');

        return AjaxResponse::success($message, $school);
    }

    public function ajaxCity(Request $request): string
    {
        $cities = $this->cityService->findBy('country_id', $request['country_id'], ['id', 'title']);

        return view('school::schools.partials.ajax-city', compact('cities'))->render();
    }

    public function ajaxRegion(Request $request): string
    {
        $regions = $this->regionService->findBy('city_id', $request['city_id'], ['id', 'title']);

        return view('school::schools.partials.ajax-region', compact('regions'))->render();
    }
}
