<?php

namespace Modules\Country\Services;

use Modules\Country\DTOs\RegionDto;
use Modules\Country\Models\Region;
use Symfony\Component\HttpFoundation\JsonResponse;
use Yajra\DataTables\Facades\DataTables;

class RegionService
{
    public function dataTable(): JsonResponse
    {
        $query = Region::query()
            ->select(['id', 'title', 'city_id', 'is_active', 'created_at'])
            ->with(['city' => function ($q): void {
                $q->select(['id', 'title', 'country_id'])
                    ->with(['country' => function ($q2): void {
                        $q2->select(['id', 'title']);
                    }]);
            }]);

        return DataTables::eloquent($query)->toJson();
    }

    public function save(RegionDto $dto): Region
    {
        return Region::create($dto->toArray())->fresh(['city.country']);
    }

    public function update(Region $region, RegionDto $dto): Region
    {
        $region->update($dto->toArray());

        return $region->fresh(['city.country']);
    }

    public function delete(Region $region): bool
    {
        return (bool) $region->delete();
    }

    public function toggleActivate(Region $region): Region
    {
        $region->update(['is_active' => ! $region->is_active]);

        return $region->fresh(['city.country']);
    }
}
