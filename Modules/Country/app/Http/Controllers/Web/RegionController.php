<?php

namespace Modules\Country\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Country\Services\RegionService;

class RegionController extends Controller
{
    public function __construct(private readonly RegionService $regionService) {}

    public function ajaxRegion(Request $request): string
    {
        $regions = $this->regionService->findBy('city_id', $request['city_id'], ['id', 'title']);

        return view('country::regions.partials.ajax', compact('regions'))->render();
    }
}
