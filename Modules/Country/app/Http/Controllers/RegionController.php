<?php

namespace Modules\Country\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Modules\Common\Helpers\AjaxResponse;
use Modules\Country\DTOs\RegionDto;
use Modules\Country\Http\Requests\RegionStoreRequest;
use Modules\Country\Http\Requests\RegionUpdateRequest;
use Modules\Country\Models\Region;
use Modules\Country\Services\CountryService;
use Modules\Country\Services\RegionService;

class RegionController extends Controller
{
    public function __construct(
        private readonly RegionService $regionService,
        private readonly CountryService $countryService,
    ) {
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->regionService->dataTable();
        }

        return view('country::regions.index', [
            'countryOptions' => $this->countryService->selectOptions(),
        ]);
    }

    public function create(): RedirectResponse
    {
        return redirect()->route('regions.index');
    }

    public function edit(Region $region): string
    {
        return view('country::regions.partials.edit', [
            'region' => $region->load('city'),
            'countryOptions' => $this->countryService->selectOptions(),
        ])->render();
    }

    public function store(RegionStoreRequest $request): JsonResponse
    {
        $dto = RegionDto::fromRequest($request);
        $region = $this->regionService->save($dto);

        return AjaxResponse::success($region);
    }

    public function update(RegionUpdateRequest $request, Region $region): JsonResponse
    {
        $dto = RegionDto::fromRequest($request);
        $region = $this->regionService->update($region, $dto);

        return AjaxResponse::success($region);
    }

    public function destroy(Region $region): JsonResponse
    {
        $status = $this->regionService->delete($region);

        return $status ? AjaxResponse::success() : AjaxResponse::error();
    }

    public function toggleActivate(Region $region): JsonResponse
    {
        $region = $this->regionService->toggleActivate($region);

        return AjaxResponse::success($region);
    }
}
