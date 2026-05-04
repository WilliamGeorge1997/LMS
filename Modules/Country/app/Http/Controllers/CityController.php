<?php

namespace Modules\Country\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Modules\Common\Helpers\ApiResponse;
use Modules\Country\DTOs\CityDto;
use Modules\Country\Http\Requests\CityStoreRequest;
use Modules\Country\Http\Requests\CityUpdateRequest;
use Modules\Country\Models\City;
use Modules\Country\Services\CityService;
use Modules\Country\Services\CountryService;

class CityController extends Controller
{
    public function __construct(
        private readonly CityService $cityService,
        private readonly CountryService $countryService,
    ) {}

    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->cityService->dataTable();
        }

        return view('country::cities.index', [
            'countryOptions' => $this->countryService->selectOptions(),
        ]);
    }

    public function create(): RedirectResponse
    {
        return redirect()->route('cities.index');
    }

    public function selectOptions(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'country_id' => ['required', 'integer', 'exists:countries,id'],
        ]);

        return response()->json(
            $this->cityService->selectOptionsByCountryId((int) $validated['country_id'])
        );
    }

    public function store(CityStoreRequest $request): JsonResponse
    {
        $dto = CityDto::fromRequest($request);
        $city = $this->cityService->save($dto);

        return ApiResponse::success($city);
    }

    public function update(CityUpdateRequest $request, City $city): JsonResponse
    {
        $dto = CityDto::fromRequest($request);
        $city = $this->cityService->update($city, $dto);

        return ApiResponse::success($city);
    }

    public function destroy(City $city): JsonResponse
    {
        $status = $this->cityService->delete($city);

        return $status ? ApiResponse::success() : ApiResponse::error();
    }

    public function toggleActivate(City $city): JsonResponse
    {
        $city = $this->cityService->toggleActivate($city);

        return ApiResponse::success($city);
    }
}
