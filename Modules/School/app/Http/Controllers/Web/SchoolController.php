<?php

namespace Modules\School\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\School\Services\SchoolService;

class SchoolController extends Controller
{
    public function __construct(private readonly SchoolService $schoolService) {}

    public function ajaxSchool(Request $request): string
    {
        $schools = $this->schoolService->active(
            countryId: $request->integer('country_id') ?: null,
            cityId: $request->integer('city_id') ?: null,
            regionId: $request->integer('region_id') ?: null,
        );

        return view('school::schools.partials.ajax', compact('schools'))->render();
    }
}
