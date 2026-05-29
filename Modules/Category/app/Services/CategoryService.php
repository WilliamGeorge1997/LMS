<?php

namespace Modules\Category\Services;

use Modules\Admin\Enums\Role;
use Modules\Category\DTOs\CategoryDto;
use Modules\Category\Models\Category;
use Symfony\Component\HttpFoundation\JsonResponse;
use Yajra\DataTables\Facades\DataTables;

class CategoryService
{
    public function findAll(array $data)
    {
        $query = Category::query()->latest('id');

        return getCaseCollection($query, $data);
    }

    public function dataTable(): JsonResponse
    {
        $query = Category::query()
            ->select(['id', 'title', 'publisher_id', 'tenant_id', 'is_active', 'created_at'])
            ->with(['publisher:id,name', 'tenant:id,name']);

        return DataTables::eloquent($query)
            ->addColumn('title_en', function (Category $category) {
                return $category->getTranslation('title', 'en');
            })
            ->addColumn('title_ar', function (Category $category) {
                return $category->getTranslation('title', 'ar');
            })
            ->toJson();
    }

    public function findBy(string $key, string $value, array $columns = ['*'])
    {
        return Category::query()
            ->active()
            ->where($key, $value)
            ->when(auth('admin')->user()->hasRole(Role::SUPER_ADMIN->value), function ($query) {
                $query->where('tenant_id', session('admin_tenant_id'));
            })
            ->get($columns);
    }

    public function active()
    {
        return Category::query()->active()->orderBy('title')->get(['id', 'title']);
    }

    public function findByTenant(array $columns = ['*'])
    {
        return Category::query()->active()
            ->when(auth('admin')->user()->hasRole(Role::SUPER_ADMIN->value), function ($query) {
                $query->where('tenant_id', session('admin_tenant_id'));
            })
            ->get($columns);
    }

    public function save(CategoryDto $dto): Category
    {
        return Category::create($dto->toArray());
    }

    public function update(Category $category, CategoryDto $dto): Category
    {
        $category->update($dto->toArray());

        return $category->fresh();
    }

    public function delete(Category $category): bool
    {
        return (bool) $category->delete();
    }

    public function toggleActivate(Category $category): Category
    {
        $category->update(['is_active' => !$category->is_active]);

        return $category->fresh();
    }
}
