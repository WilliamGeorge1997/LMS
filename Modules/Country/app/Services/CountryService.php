<?php

namespace Modules\Country\Services;

use Modules\Country\DTOs\CountryDto;
use Modules\Country\Models\Country;
use Symfony\Component\HttpFoundation\JsonResponse;
use Yajra\DataTables\Facades\DataTables;

class CountryService
{
    public function dataTable(): JsonResponse
    {
        $query = Country::query()
            ->select(['id', 'title', 'tenant_id', 'is_active', 'created_at'])
            ->with(['tenant:id,name'])
            ->latest('id');

        return DataTables::eloquent($query)
            ->addColumn('title_en', function (Country $country) {
                return $country->getTranslation('title', 'en');
            })
            ->addColumn('title_ar', function (Country $country) {
                return $country->getTranslation('title', 'ar');
            })
            ->toJson();
    }

    public function active()
    {
        return Country::query()->active()->orderBy('id')->get(['id', 'title']);
    }

    public function findByTenant(array $columns = ['*'])
    {
        return Country::query()->active()->byTenant()->orderBy('id')->get($columns);
    }

    public function save(CountryDto $dto): Country
    {
        return Country::create($dto->toArray())->fresh();
    }

    public function update(Country $country, CountryDto $dto): Country
    {
        $country->update($dto->toArray());

        return $country->fresh();
    }

    public function delete(Country $country): bool
    {
        return (bool) $country->delete();
    }

    public function toggleActivate(Country $country): Country
    {
        $country->update(['is_active' => ! $country->is_active]);

        return $country->fresh();
    }
}
