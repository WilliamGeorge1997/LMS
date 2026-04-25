<?php

namespace Modules\Admin\Services;

use Illuminate\Database\Eloquent\Builder;
use Modules\Admin\Models\Admin;

class AdminService
{
    public function findAll(array $data)
    {
        $query = Admin::query()->latest('id');

        return getCaseCollection($query, $data);
    }

    public function queryForDataTable(): Builder
    {
        $query = Admin::query()
            ->select(['id', 'name', 'email', 'image', 'is_active', 'created_at']);

        // Keep default order by id desc only when no explicit DataTables sorting is requested.
        if (blank(request()->input('order'))) {
            $query->latest('id');
        }

        return $query;
    }
}
