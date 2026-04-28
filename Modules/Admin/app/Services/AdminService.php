<?php

namespace Modules\Admin\Services;

use Modules\Admin\Models\Admin;
use Modules\Common\Traits\UploaderTrait;
use Symfony\Component\HttpFoundation\JsonResponse;
use Yajra\DataTables\Facades\DataTables;

class AdminService
{
    use UploaderTrait;
    private string $uploadFolder = 'admin';
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

    public function save(array $data): Admin
    {
        if (request()->hasFile('image')) 
            $data['image'] = $this->uploadImage(request()->file('image'), $this->uploadFolder);

        $admin = Admin::create($data);
        $admin->assignRole($data['role']);
        return $admin;
    }
}
