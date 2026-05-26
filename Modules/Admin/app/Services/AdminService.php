<?php

namespace Modules\Admin\Services;

use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Http\UploadedFile;
use Modules\Admin\DTOs\AdminDto;
use Modules\Admin\Enums\Role;
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

    public function findActiveManagers(array $data = [])
    {
        $query = Admin::query()->active()->role(Role::MANAGER);

        return getCaseCollection($query, $data);
    }

    public function dataTable(): JsonResponse
    {
        $query = Admin::query()
            ->select(['id', 'name', 'email', 'image', 'tenant_id', 'is_active', 'created_at'])
            ->where('id', '!=', auth('admin')->id())
            ->with([
                'roles' => function (MorphToMany $q) {
                    $q->select(['id', 'name']);
                },
                'tenant:id,name',
            ]);

        return DataTables::eloquent($query)
            ->addColumn('role', function (Admin $admin) {
                return $admin->roles->first()?->name ?? '';
            })
            ->toJson();
    }

    public function save(AdminDto $dto, ?UploadedFile $image = null): Admin
    {
        $data = $dto->toArray();
        if ($image) {
            $data['image'] = $this->uploadImage($image, $this->uploadFolder);
        }

        $admin = Admin::create($data);
        $admin->assignRole($dto->role);

        return $admin;
    }

    public function update(Admin $admin, AdminDto $dto, ?UploadedFile $image = null): Admin
    {
        $data = $dto->toArray();
        if ($image) {
            if ($admin->image) {
                $this->deleteFile($this->uploadFolder, $admin->getRawOriginal('image'));
            }

            $data['image'] = $this->uploadImage($image, $this->uploadFolder);
        }

        $admin->update($data);
        $admin->syncRoles($dto->role);

        return $admin->fresh();
    }

    public function delete(Admin $admin): bool
    {
        if ($admin->image) {
            $this->deleteFile($this->uploadFolder, $admin->getRawOriginal('image'));
        }

        return (bool) $admin->delete();
    }

    public function toggleActivate(Admin $admin): Admin
    {
        $admin->update(['is_active' => ! $admin->is_active]);

        return $admin->fresh();
    }
}
