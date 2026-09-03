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
            ->select(['id', 'title', 'city_id', 'tenant_id', 'is_active', 'created_at'])
            ->with([
                'city:id,title,country_id',
                'city.country:id,title',
                'tenant:id,name',
            ])
            ->latest('id');

        return DataTables::eloquent($query)
            ->addColumn('title_en', function (Region $region) {
                return $region->getTranslation('title', 'en');
            })
            ->addColumn('title_ar', function (Region $region) {
                return $region->getTranslation('title', 'ar');
            })
            ->toJson();
    }

    public function findBy(string $key, string|int $value, array $columns = ['*'])
    {
        return Region::query()->active()->byTenant()->where($key, $value)->orderBy('id')->get($columns);
    }

    public function findByTenant(array $columns = ['*'])
    {
        return Region::query()->active()->byTenant()->orderBy('id')->get($columns);
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
