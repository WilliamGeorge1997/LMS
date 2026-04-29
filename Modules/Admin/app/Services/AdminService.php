<?php

namespace Modules\Admin\Services;

use Illuminate\Database\Eloquent\Relations\MorphToMany;
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
        $query = Admin::query()
            ->select(['id', 'name', 'email', 'image', 'is_active', 'created_at'])
            ->with(['roles' => function (MorphToMany $q) {
                $q->select(['id', 'name']);
            }]);

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

    public function update(Admin $admin, array $data)
    {
        if (request()->hasFile('image')) {
            $this->deleteFile($this->uploadFolder, $admin->image);
            $data['image'] = $this->uploadImage(request()->file('image'), $this->uploadFolder);
        }
        $admin->update($data);
        $admin->syncRoles($data['role']);
        return $admin->fresh();
    }

    public function delete(Admin $admin)
    {
        if ($admin->image)
            $this->deleteFile($this->uploadFolder, $admin->getRawOriginal('image'));

        $admin->delete();
        return $admin;
    }

    public function toggleActivate(Admin $admin)
    {
        $admin->update(['is_active' => !$admin->is_active]);
        return $admin->fresh();
    }
}
