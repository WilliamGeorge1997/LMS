<?php

namespace Modules\Publisher\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Modules\Common\Helpers\ApiResponse;
use Modules\Publisher\DTOs\PublisherDto;
use Modules\Publisher\Http\Requests\PublisherRequest;
use Modules\Publisher\Models\Publisher;
use Modules\Publisher\Services\PublisherService;

class PublisherController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            'set.locale',
            // 'role:Super Admin|Manager'
        ];
    }

    public function __construct(private readonly PublisherService $publisherService) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->publisherService->dataTable();
        }

        return view('publisher::publishers.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('publisher::create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PublisherRequest $request): JsonResponse
    {
        $dto = PublisherDto::fromRequest($request);
        $admin = $this->publisherService->save($dto);

        return ApiResponse::success(__('publisher::messages.created_sucessfully'), $admin);
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
    public function update(PublisherRequest $request, Publisher $publisher): JsonResponse
    {
        $dto = PublisherDto::fromRequest($request);
        $publisher = $this->publisherService->update($publisher, $dto);

        return ApiResponse::success(__('publisher::messages.updated_sucessfully'), $publisher);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Publisher $publisher): JsonResponse
    {
        $status = $this->publisherService->delete($publisher);

        return $status
            ? ApiResponse::success(__('publisher::messages.deleted_sucessfully'))
            : ApiResponse::error();
    }

    public function toggleActivate(Publisher $publisher): JsonResponse
    {
        $publisher = $this->publisherService->toggleActivate($publisher);
        $message = $publisher->is_active
            ? __('publisher::messages.activated_sucessfully')
            : __('publisher::messages.deactivated_sucessfully');

        return ApiResponse::success($message, $publisher);
    }
}
