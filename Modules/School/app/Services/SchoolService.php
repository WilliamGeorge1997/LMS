<?php

namespace Modules\School\Services;

use Modules\School\DTOs\SchoolDto;
use Modules\School\Models\School;
use Symfony\Component\HttpFoundation\JsonResponse;
use Yajra\DataTables\Facades\DataTables;

class SchoolService
{
    public function dataTable(): JsonResponse
    {
        $query = School::query()
            ->select([
                'id',
                'title',
                'phone',
                'email',
                'country_id',
                'city_id',
                'region_id',
                'tenant_id',
                'is_active',
                'created_at',
            ])
            ->with([
                'country:id,title',
                'city:id,title',
                'region:id,title',
                'tenant:id,name',
            ])
            ->byTenant()
            ->latest('id');

        return DataTables::eloquent($query)
            ->addColumn('title_en', function (School $school) {
                return $school->getTranslation('title', 'en');
            })
            ->addColumn('title_ar', function (School $school) {
                return $school->getTranslation('title', 'ar');
            })
            ->toJson();
    }

    public function findBy(string $key, string|int $value, array $columns = ['id', 'title', 'phone', 'email', 'country_id', 'city_id', 'region_id'])
    {
        return School::query()->active()->byTenant()->where($key, $value)->orderBy('id')->get($columns);
    }

    public function findByTenant(array $columns = ['id', 'title', 'phone', 'email', 'country_id', 'city_id', 'region_id'])
    {
        return School::query()->active()->byTenant()->orderBy('id')->get($columns);
    }

    public function active(?int $countryId = null, ?int $cityId = null, ?int $regionId = null, array $columns = ['id', 'title', 'phone', 'email', 'country_id', 'city_id', 'region_id'])
    {
        return School::query()
            ->active()
            ->byTenant()
            ->when($countryId, fn ($query) => $query->where('country_id', $countryId))
            ->when($cityId, fn ($query) => $query->where('city_id', $cityId))
            ->when($regionId, fn ($query) => $query->where('region_id', $regionId))
            ->orderBy('id')
            ->get($columns);
    }

    public function save(SchoolDto $dto): School
    {
        return School::create($dto->toArray());
    }

    public function update(School $school, SchoolDto $dto): School
    {
        $school->update($dto->toArray());

        return $school->fresh(['country', 'city', 'region', 'tenant']);
    }

    public function delete(School $school): bool
    {
        return (bool) $school->delete();
    }

    public function toggleActivate(School $school): School
    {
        $school->update(['is_active' => ! $school->is_active]);

        return $school->fresh(['country', 'city', 'region', 'tenant']);
    }
}
