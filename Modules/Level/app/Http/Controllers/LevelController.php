<?php

namespace Modules\Level\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Modules\Admin\Enums\Role;
use Modules\Common\Helpers\AjaxResponse;
use Modules\Level\DTOs\LevelDto;
use Modules\Level\Http\Requests\LevelStoreRequest;
use Modules\Level\Http\Requests\LevelUpdateRequest;
use Modules\Level\Models\Level;
use Modules\Level\Services\LevelService;
use Modules\Level\ViewModel\LevelViewModel;

class LevelController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            'auth:admin',
            'role:'.Role::SUPER_ADMIN->value.'|'.Role::MANAGER->value,
            'set.locale',
        ];
    }

    public function __construct(private readonly LevelService $levelService) {}

    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->levelService->dataTable();
        }

        $viewModel = new LevelViewModel;

        return view('level::levels.index', compact('viewModel'));
    }

    public function store(LevelStoreRequest $request): JsonResponse
    {
        $dto = LevelDto::fromRequest($request);
        $level = $this->levelService->save($dto);

        return AjaxResponse::success(__('level::messages.created_successfully'), $level);
    }

    public function edit(Level $level): string
    {
        $viewModel = new LevelViewModel;

        return view('level::levels.partials.edit', [
            'level' => $level->load(['publisher', 'category', 'tenant']),
            'viewModel' => $viewModel,
        ])->render();
    }

    public function update(LevelUpdateRequest $request, Level $level): JsonResponse
    {
        $dto = LevelDto::fromRequest($request);
        $level = $this->levelService->update($level, $dto);

        return AjaxResponse::success(__('level::messages.updated_successfully'), $level);
    }

    public function destroy(Level $level): JsonResponse
    {
        $status = $this->levelService->delete($level);

        return $status
            ? AjaxResponse::success(__('level::messages.deleted_successfully'))
            : AjaxResponse::error();
    }

    public function toggleActivate(Level $level): JsonResponse
    {
        $level = $this->levelService->toggleActivate($level);
        $message = $level->is_active
            ? __('level::messages.activated_successfully')
            : __('level::messages.deactivated_successfully');

        return AjaxResponse::success($message, $level);
    }
}
