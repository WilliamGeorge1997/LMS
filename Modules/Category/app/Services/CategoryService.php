<?php

namespace Modules\Category\Services;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Category\DTOs\CategoryDto;
use Modules\Category\Models\Category;
use Symfony\Component\HttpFoundation\JsonResponse;
use Yajra\DataTables\Facades\DataTables;

class CategoryService
{
    public function dataTable(): JsonResponse
    {
        $query = Category::query()
            ->available()
            ->select(['id', 'name', 'publisher_id', 'manager_id', 'is_active', 'created_at'])
            ->with([
                'manager' => function (BelongsTo $q) {
                    $q->select(['id', 'name']);
                },
                'publisher' => function ($q) {
                    $q->select(['id', 'name']);
                },
            ]);

        return DataTables::eloquent($query)
            ->addColumn('name_display', function (Category $category) {
                return ($category->getTranslation('name', 'ar') ?: '') .
                    ' - ' .
                    ($category->getTranslation('name', 'en') ?: '');
            })
            ->addColumn('name_ar', fn(Category $c) => $c->getTranslation('name', 'ar'))
            ->addColumn('name_en', fn(Category $c) => $c->getTranslation('name', 'en'))
            ->toJson();
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