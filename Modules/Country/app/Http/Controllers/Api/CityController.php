<?php

namespace Modules\Country\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\Country\Models\Country;
use Modules\Country\Services\CityService;

class CityController extends Controller
{
    public function __construct(private readonly CityService $cityService)
    {
    }

    public function index(Country $country): JsonResponse
    {
        $cities = $this->cityService->findBy('country_id', $country->id, ['id', 'title', 'country_id']);

        return apiResponse(
            true,
            __('country::messages.cities_retrieved_successfully'),
            $cities
        );
    }
}
