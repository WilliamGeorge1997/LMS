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
            ->select(['id', 'title', 'is_active', 'created_at']);

        return DataTables::eloquent($query)->toJson();
    }

    /**
     * @return list<array{value: int, label: string}>
     */
    public function selectOptions(): array
    {
        return Country::query()
            ->orderBy('id')
            ->get(['id', 'title'])
            ->map(fn (Country $c): array => [
                'value' => $c->id,
                'label' => (string) $c->title,
            ])
            ->values()
            ->all();
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
