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
            ->with(['country' => function ($q): void {
                $q->select(['id', 'title']);
            }]);

        return DataTables::eloquent($query)->toJson();
    }

    /**
     * @return list<array{value: int, label: string}>
     */
    public function selectOptionsByCountryId(int $countryId): array
    {
        return City::query()
            ->where('country_id', $countryId)
            ->orderBy('id')
            ->get(['id', 'title'])
            ->map(fn (City $c): array => [
                'value' => $c->id,
                'label' => (string) $c->title,
            ])
            ->values()
            ->all();
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
