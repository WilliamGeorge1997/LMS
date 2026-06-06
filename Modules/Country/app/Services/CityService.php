<?php

namespace Modules\Country\Services;

use Modules\Country\DTOs\CityDto;
use Modules\Country\Models\City;
use Symfony\Component\HttpFoundation\JsonResponse;
use Yajra\DataTables\Facades\DataTables;

class CityService
{
    public function dataTable(): JsonResponse
    {
        $query = City::query()
            ->select(['id', 'title', 'country_id', 'is_active', 'created_at'])
            ->with(['country:id,title']);

        return DataTables::eloquent($query)
            ->addColumn('title_en', function (City $city) {
                return $city->getTranslation('title', 'en');
            })
            ->addColumn('title_ar', function (City $city) {
                return $city->getTranslation('title', 'ar');
            })
            ->toJson();
    }

    public function findBy(string $key, string $value, array $columns = ['*'])
    {
        return City::query()->active()->where($key, $value)->orderBy('id')->get($columns);
    }

    public function save(CityDto $dto): City
    {
        return City::create($dto->toArray())->fresh(['country']);
    }

    public function update(City $city, CityDto $dto): City
    {
        $city->update($dto->toArray());

        return $city->fresh(['country']);
    }

    public function delete(City $city): bool
    {
        return (bool) $city->delete();
    }

    public function toggleActivate(City $city): City
    {
        $city->update(['is_active' => ! $city->is_active]);

        return $city->fresh(['country']);
    }
}
