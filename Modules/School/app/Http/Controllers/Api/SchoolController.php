<?php

namespace Modules\School\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\School\Services\SchoolService;

class SchoolController extends Controller
{
    public function __construct(private readonly SchoolService $schoolService)
    {
    }

    public function index(): JsonResponse
    {
        $schools = $this->schoolService->findByTenant();

        return apiResponse(
            true,
            __('school::messages.schools_retrieved_successfully'),
            $schools
        );
    }
}
