<?php

namespace Modules\Publisher\Services;

use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Modules\Publisher\DTOs\PublisherDto;
use Modules\Publisher\Models\Publisher;
use Symfony\Component\HttpFoundation\JsonResponse;
use Yajra\DataTables\Facades\DataTables;

class PublisherService
{
    public function findAll(array $data)
    {
        $query = Publisher::query()->available()->latest('id');

        return getCaseCollection($query, $data);
    }

    public function dataTable(): JsonResponse
    {
        $query = Publisher::query()
            ->available()
            ->select(['id', 'name', 'is_active', 'created_at'])
            ->with(['manager' => function (MorphToMany $q) {
                $q->select(['id', 'name']);
            }]);

        return DataTables::eloquent($query)->toJson();
    }

    public function save(PublisherDto $dto): Publisher
    {
        $data = $dto->toArray();
        $admin = Publisher::create($data);

        return $admin;
    }

    public function update(Publisher $publisher, PublisherDto $dto): Publisher
    {
        $data = $dto->toArray();

        $publisher->update($data);

        return $publisher->fresh();
    }

    public function delete(Publisher $publisher): bool
    {
        return (bool) $publisher->delete();
    }

    public function toggleActivate(Publisher $publisher): Publisher
    {
        $publisher->update(['is_active' => ! $publisher->is_active]);

        return $publisher->fresh();
    }
}
