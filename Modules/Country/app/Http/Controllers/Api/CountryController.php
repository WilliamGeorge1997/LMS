<?php

namespace Modules\Country\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\Country\Services\CountryService;

class CountryController extends Controller
{
    public function __construct(private readonly CountryService $countryService)
    {
    }

    public function index(): JsonResponse
    {
        $countries = $this->countryService->active();

        return apiResponse(
            true,
            __('country::messages.countries_retrieved_successfully'),
            $countries
        );
    }
}
