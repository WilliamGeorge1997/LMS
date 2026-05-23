<?php

namespace Modules\Category\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Modules\Admin\Enums\Role;
use Modules\Common\Helpers\AjaxResponse;
use Modules\Category\DTOs\CategoryDto;
use Modules\Category\Http\Requests\CategoryRequest;
use Modules\Category\Models\Category;
use Modules\Category\Services\CategoryService;
use Modules\Category\ViewModel\CategoryViewModel;

class CategoryController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            'auth:admin',
            'role:' . Role::SUPER_ADMIN->value . '|' . Role::MANAGER->value,
            'set.locale',
        ];
    }

    public function __construct(private readonly CategoryService $categoryService)
    {
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->categoryService->dataTable();
        }

        $viewModel = new CategoryViewModel();

        return view('category::categories.index', compact('viewModel'));
    }

    public function store(CategoryRequest $request): JsonResponse
    {
        $dto = CategoryDto::fromRequest($request);
        $category = $this->categoryService->save($dto);

        return AjaxResponse::success(__('category::messages.created_successfully'), $category);
    }

    public function update(CategoryRequest $request, Category $category): JsonResponse
    {
        $dto = CategoryDto::fromRequest($request);
        $category = $this->categoryService->update($category, $dto);

        return AjaxResponse::success(__('category::messages.updated_successfully'), $category);
    }

    public function destroy(Category $category): JsonResponse
    {
        $status = $this->categoryService->delete($category);

        return $status
            ? AjaxResponse::success(__('category::messages.deleted_successfully'))
            : AjaxResponse::error();
    }

    public function toggleActivate(Category $category): JsonResponse
    {
        $category = $this->categoryService->toggleActivate($category);
        $message = $category->is_active
            ? __('category::messages.activated_successfully')
            : __('category::messages.deactivated_successfully');

        return AjaxResponse::success($message, $category);
    }
}