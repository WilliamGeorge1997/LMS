<?php

namespace Modules\Publisher\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Modules\Admin\Enums\Role;
use Modules\Common\Helpers\ApiResponse;
use Modules\Publisher\DTOs\PublisherDto;
use Modules\Publisher\Http\Requests\PublisherRequest;
use Modules\Publisher\Models\Publisher;
use Modules\Publisher\Services\PublisherService;
use Modules\Publisher\ViewModel\PublisherViewModel;

class PublisherController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            'auth:admin',
            'role:' . Role::SUPER_ADMIN->value . '|' . Role::MANAGER->value,
            'set.locale',
        ];
    }

    public function __construct(private readonly PublisherService $publisherService)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->publisherService->dataTable();
        }
        $viewModel = new PublisherViewModel();

        return view('publisher::publishers.index', compact('viewModel'));
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

    public function byManager(int $manager_id)
    {
        $publishers = $this->publisherService->findBy('manager_id', $manager_id, ['id', 'name']);
        return ApiResponse::success(data: $publishers);
    }
}
