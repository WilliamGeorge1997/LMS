<?php

namespace Modules\Country\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Country\Services\CityService;

class CityController extends Controller
{
    public function __construct(private readonly CityService $cityService) {}

    public function ajaxCity(Request $request): string
    {
        $cities = $this->cityService->findBy('country_id', $request['country_id'], ['id', 'title']);

        return view('country::cities.partials.ajax', compact('cities'))->render();
    }
}
