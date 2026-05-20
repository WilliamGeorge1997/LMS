<?php

namespace Modules\Tenant\Services;

use Modules\Tenant\DTOs\TenantDto;
use Modules\Tenant\Models\Tenant;
use Symfony\Component\HttpFoundation\JsonResponse;
use Yajra\DataTables\Facades\DataTables;

class TenantService
{
    public function dataTable(): JsonResponse
    {
        $query = Tenant::query()
            ->select(['id', 'name', 'is_active', 'created_at'])
            ->with(['domains:id,domain,tenant_id']);

        return DataTables::eloquent($query)
            ->addColumn('name_en', function (Tenant $tenant) {
                return $tenant->getTranslation('name', 'en');
            })
            ->addColumn('name_ar', function (Tenant $tenant) {
                return $tenant->getTranslation('name', 'ar');
            })
            ->addColumn('domain', function (Tenant $tenant) {
                return $tenant->domains->first()?->domain;
            })
            ->filterColumn('name_en', function ($query, $keyword) {
                $query->whereJsonContainsLocale('name', 'en', "%{$keyword}%", 'like');
            })
            ->filterColumn('name_ar', function ($query, $keyword) {
                $query->whereJsonContainsLocale('name', 'ar', "%{$keyword}%", 'like');
            })
            ->filterColumn('domain', function ($query, $keyword) {
                $query->whereHas('domains', function ($domainQuery) use ($keyword) {
                    $domainQuery->where('domain', 'like', "%{$keyword}%");
                });
            })
            ->toJson();
    }

    public function save(TenantDto $dto): Tenant
    {
        $tenant = Tenant::create($dto->toArray());
        $tenant->domains()->create(['domain' => $dto->domain]);

        return $tenant->load('domains');
    }

    public function update(Tenant $tenant, TenantDto $dto): Tenant
    {
        $tenant->update($dto->toArray());

        $tenant->domains()->updateOrCreate(
            ['tenant_id' => $tenant->id],
            ['domain' => $dto->domain],
        );

        return $tenant->fresh(['domains']);
    }

    public function delete(Tenant $tenant): bool
    {
        return (bool) $tenant->delete();
    }

    public function toggleActivate(Tenant $tenant): Tenant
    {
        $tenant->update(['is_active' => !$tenant->is_active]);

        return $tenant->fresh(['domains']);
    }

    public function editRowPartial(Tenant $tenant): string
    {
        return view('tenant::tenants._edit_row', [
            'tenant' => $tenant->load('domains'),
        ])->render();
    }
}
