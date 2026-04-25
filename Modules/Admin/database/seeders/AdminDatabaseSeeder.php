<?php

namespace Modules\Admin\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Modules\Admin\Models\Admin;
use Spatie\Permission\Models\Role;
use Modules\Admin\Enums\Role as RoleEnum;

class AdminDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $superAdmin = $this->createSuperAdmin();
        $superAdminRole = $this->createSuperAdminRole();
        $superAdmin->assignRole($superAdminRole);
    }

    private function createSuperAdmin(): Admin
    {
        return Admin::create([
            'name' => 'Super Admin',
            'email' => 'admin@admin.com',
            'password' => Hash::make('123123'),
            'image' => null,
            'is_active' => true,
        ]);
    }

    private function createSuperAdminRole(): Role
    {
        return Role::create([
            'name' => RoleEnum::SUPER_ADMIN->value,
            'guard_name' => 'admin',
        ]);
    }
}
