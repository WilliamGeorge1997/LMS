<?php

namespace Modules\Country\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\Country\Models\City;
use Modules\Country\Services\RegionService;

class ZoneController extends Controller
{
    public function __construct(private readonly RegionService $regionService)
    {
    }

    public function index(City $city): JsonResponse
    {
        $zones = $this->regionService->findBy('city_id', $city->id, ['id', 'title', 'city_id']);

        return apiResponse(
            true,
            __('country::messages.zones_retrieved_successfully'),
            $zones
        );
    }
}
