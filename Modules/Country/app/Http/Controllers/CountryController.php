<?php

namespace Modules\Country\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Modules\Common\Helpers\ApiResponse;
use Modules\Country\DTOs\CountryDto;
use Modules\Country\Http\Requests\CountryStoreRequest;
use Modules\Country\Http\Requests\CountryUpdateRequest;
use Modules\Country\Models\Country;
use Modules\Country\Services\CountryService;

class CountryController extends Controller
{
    public function __construct(private readonly CountryService $countryService) {}

    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->countryService->dataTable();
        }

        return view('country::countries.index');
    }

    public function create(): RedirectResponse
    {
        return redirect()->route('countries.index');
    }

    public function selectOptions(): JsonResponse
    {
        return response()->json($this->countryService->selectOptions());
    }

    public function store(CountryStoreRequest $request): JsonResponse
    {
        $dto = CountryDto::fromRequest($request);
        $country = $this->countryService->save($dto);

        return ApiResponse::success($country);
    }

    public function update(CountryUpdateRequest $request, Country $country): JsonResponse
    {
        $dto = CountryDto::fromRequest($request);
        $country = $this->countryService->update($country, $dto);

        return ApiResponse::success($country);
    }

    public function destroy(Country $country): JsonResponse
    {
        $status = $this->countryService->delete($country);

        return $status ? ApiResponse::success() : ApiResponse::error();
    }

    public function toggleActivate(Country $country): JsonResponse
    {
        $country = $this->countryService->toggleActivate($country);

        return ApiResponse::success($country);
    }
}
