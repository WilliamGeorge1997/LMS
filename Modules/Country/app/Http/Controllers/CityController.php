<?php

namespace Modules\Country\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Modules\Admin\Enums\Role;
use Modules\Common\Helpers\AjaxResponse;
use Modules\Country\DTOs\CityDto;
use Modules\Country\Http\Requests\CityStoreRequest;
use Modules\Country\Http\Requests\CityUpdateRequest;
use Modules\Country\Models\City;
use Modules\Country\Services\CityService;
use Modules\Country\ViewModel\CountryViewModel;

class CityController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            'auth:admin',
            'role:' . Role::SUPER_ADMIN->value,
            'set.locale',
        ];
    }

    public function __construct(private readonly CityService $cityService)
    {
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->cityService->dataTable();
        }

        $viewModel = new CountryViewModel;

        return view('country::cities.index', compact('viewModel'));
    }

    public function store(CityStoreRequest $request): JsonResponse
    {
        $dto = CityDto::fromRequest($request);
        $city = $this->cityService->save($dto);

        return AjaxResponse::success(__('country::messages.city_created_successfully'), $city);
    }

    public function edit(City $city): string
    {
        $viewModel = new CountryViewModel;

        return view('country::cities.partials.edit', [
            'city' => $city->load('country'),
            'viewModel' => $viewModel,
        ])->render();
    }

    public function update(CityUpdateRequest $request, City $city): JsonResponse
    {
        $dto = CityDto::fromRequest($request);
        $city = $this->cityService->update($city, $dto);

        return AjaxResponse::success(__('country::messages.city_updated_successfully'), $city);
    }

    public function destroy(City $city): JsonResponse
    {
        $status = $this->cityService->delete($city);

        return $status
            ? AjaxResponse::success(__('country::messages.city_deleted_successfully'))
            : AjaxResponse::error();
    }

    public function toggleActivate(City $city): JsonResponse
    {
        $city = $this->cityService->toggleActivate($city);
        $message = $city->is_active
            ? __('country::messages.city_activated_successfully')
            : __('country::messages.city_deactivated_successfully');

        return AjaxResponse::success($message, $city);
    }

    public function ajaxCity(Request $request): string
    {
        $cities = $this->cityService->findBy('country_id', $request['country_id'], ['id', 'title']);

        return view('country::cities.partials.ajax', compact('cities'))->render();
    }
}
