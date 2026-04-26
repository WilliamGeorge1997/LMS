<?php

namespace Modules\Admin\Services;

use Modules\Admin\Models\Admin;
use Symfony\Component\HttpFoundation\JsonResponse;
use Yajra\DataTables\Facades\DataTables;

class AdminService
{
    public function findAll(array $data)
    {
        $query = Admin::query()->latest('id');

        return getCaseCollection($query, $data);
    }

    public function dataTable(): JsonResponse
    {
        $query = Admin::query()->select(['id', 'name', 'email', 'image', 'is_active', 'created_at']);
        return DataTables::eloquent($query)->toJson();
    }

    public function save($data){
        return Admin::create($data);
    }
}
