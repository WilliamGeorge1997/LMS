<?php

namespace Modules\Level\Services;

use Modules\Level\DTOs\LevelDto;
use Modules\Level\Models\Level;
use Symfony\Component\HttpFoundation\JsonResponse;
use Yajra\DataTables\Facades\DataTables;

class LevelService
{
    public function findAll(array $data)
    {
        $query = Level::query()->latest('id');

        return getCaseCollection($query, $data);
    }

    public function dataTable(): JsonResponse
    {
        $query = Level::query()
            ->select(['id', 'title', 'publisher_id', 'category_id', 'tenant_id', 'is_active', 'created_at'])
            ->with(['publisher:id,name', 'category:id,title', 'tenant:id,name']);

        return DataTables::eloquent($query)
            ->addColumn('title_en', function (Level $level) {
                return $level->getTranslation('title', 'en');
            })
            ->addColumn('title_ar', function (Level $level) {
                return $level->getTranslation('title', 'ar');
            })
            ->toJson();
    }

    public function findBy(string $key, string $value, array $columns = ['*'])
    {
        return Level::query()->active()->where($key, $value)->get($columns);
    }

    public function active()
    {
        return Level::query()->active()->orderBy('title')->get(['id', 'title']);
    }

    public function save(LevelDto $dto): Level
    {
        return Level::create($dto->toArray());
    }

    public function update(Level $level, LevelDto $dto): Level
    {
        $level->update($dto->toArray());

        return $level->fresh();
    }

    public function delete(Level $level): bool
    {
        return (bool) $level->delete();
    }

    public function toggleActivate(Level $level): Level
    {
        $level->update(['is_active' => ! $level->is_active]);

        return $level->fresh();
    }
}
