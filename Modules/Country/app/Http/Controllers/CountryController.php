<?php

namespace Modules\Country\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Modules\Country\DTOs\CountryDto;
use Modules\Country\Http\Requests\CountryStoreRequest;
use Modules\Country\Http\Requests\CountryUpdateRequest;
use Modules\Country\Models\Country;
use Modules\Country\Services\CountryService;
use Modules\Common\Helpers\AjaxResponse;

class CountryController extends Controller implements HasMiddleware
{

    public static function middleware(): array
    {
        return ['set.locale'];
    }

    public function __construct(private readonly CountryService $countryService)
    {
    }

    /**
     * Display a listing of the resource.
     */

    public function index(Request $request)
    {
        if ($request->ajax())
            return $this->countryService->dataTable();
        return view('country::countries.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('country::countries.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CountryStoreRequest $request): JsonResponse
    {
        $dto = CountryDto::fromRequest($request);
        $country = $this->countryService->save($dto);
        return AjaxResponse::success('Country created successfully.', $country);
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('country::countries.show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return view('country::countries.edit');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CountryUpdateRequest $request, Country $country): JsonResponse
    {
        $dto = CountryDto::fromRequest($request);
        $country = $this->countryService->update($country, $dto);
        return AjaxResponse::success($country);
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Country $country): JsonResponse
    {
        $status = $this->countryService->delete($country);
        return $status ? AjaxResponse::success() : AjaxResponse::error();
    }

    public function toggleActivate(Country $country): JsonResponse
    {
        $country = $this->countryService->toggleActivate($country);
        return AjaxResponse::success($country);
    }
}
