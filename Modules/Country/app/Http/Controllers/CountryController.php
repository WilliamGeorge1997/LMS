<?php

namespace Modules\Country\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Modules\Admin\Enums\Role;
use Modules\Common\Helpers\AjaxResponse;
use Modules\Country\DTOs\CountryDto;
use Modules\Country\Http\Requests\CountryStoreRequest;
use Modules\Country\Http\Requests\CountryUpdateRequest;
use Modules\Country\Models\Country;
use Modules\Country\Services\CountryService;

class CountryController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            'auth:admin',
            'role:' . Role::SUPER_ADMIN->value,
            'set.locale',
        ];
    }

    public function __construct(private readonly CountryService $countryService)
    {
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->countryService->dataTable();
        }

        return view('country::countries.index');
    }

    public function store(CountryStoreRequest $request): JsonResponse
    {
        $dto = CountryDto::fromRequest($request);
        $country = $this->countryService->save($dto);

        return AjaxResponse::success(__('country::messages.country_created_successfully'), $country);
    }

    public function edit(Country $country): string
    {
        return view('country::countries.partials.edit', ['country' => $country])->render();
    }

    public function update(CountryUpdateRequest $request, Country $country): JsonResponse
    {
        $dto = CountryDto::fromRequest($request);
        $country = $this->countryService->update($country, $dto);

        return AjaxResponse::success(__('country::messages.country_updated_successfully'), $country);
    }

    public function destroy(Country $country): JsonResponse
    {
        $status = $this->countryService->delete($country);

        return $status
            ? AjaxResponse::success(__('country::messages.country_deleted_successfully'))
            : AjaxResponse::error();
    }

    public function toggleActivate(Country $country): JsonResponse
    {
        $country = $this->countryService->toggleActivate($country);
        $message = $country->is_active
            ? __('country::messages.country_activated_successfully')
            : __('country::messages.country_deactivated_successfully');

        return AjaxResponse::success($message, $country);
    }
}
