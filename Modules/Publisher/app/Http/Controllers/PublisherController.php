<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Modules\Publisher\DTOs\PublisherDto;
use Modules\Admin\Http\Requests\PublisherRequest;
use Modules\Publisher\Models\Publisher;
use Modules\Publisher\Services\PublisherService;
use Modules\Common\Helpers\ApiResponse;

class PublisherController extends Controller implements HasMiddleware
{

    public static function middleware(): array
    {
        return [
            'set.locale',
            // 'role:Super Admin|Manager'
        ];
    }

    public function __construct(private readonly AdminService $adminService) {}

    /**
     * Display a listing of the resource.
     */

    public function index(Request $request)
    {
        if ($request->ajax())
            return $this->adminService->dataTable();
        return view('admin::admins.index');
    }

    public function dashboard(Request $request)
    {
        return view('admin::dashboard');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin::create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AdminStoreRequest $request): JsonResponse
    {
        $dto   = AdminDto::fromRequest($request);
        $admin = $this->adminService->save($dto, $request->file('image'));
        return ApiResponse::success(__('admin::attributes.admin_created_sucessfully'), $admin);
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('admin::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return view('admin::edit');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AdminUpdateRequest $request, Admin $admin): JsonResponse
    {
        $dto   = AdminDto::fromRequest($request);
        $admin = $this->adminService->update($admin, $dto, $request->file('image'));
        return ApiResponse::success(__('admin::attributes.admin_updated_sucessfully'), $admin);
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Admin $admin): JsonResponse
    {
        $status = $this->adminService->delete($admin);
        return $status ? ApiResponse::success('test') : ApiResponse::error();
    }

    public function toggleActivate(Admin $admin): JsonResponse
    {
        $admin = $this->adminService->toggleActivate($admin);
        return ApiResponse::success('test', $admin);
    }
}
