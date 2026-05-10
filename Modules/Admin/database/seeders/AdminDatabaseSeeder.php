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
        $super_admin = $this->storeSuperAdmin();
        $super_admin_role = $this->storeSuperAdminRole();
        $super_admin->assignRole($super_admin_role);
        $this->storeManagerRole();
    }

    private function storeSuperAdmin(): Admin
    {
        return Admin::create([
            'name' => 'Super Admin',
            'email' => 'admin@admin.com',
            'password' => Hash::make('123123'),
            'image' => null,
            'is_active' => true,
        ]);
    }

    private function storeSuperAdminRole(): Role
    {
        return Role::create([
            'name' => RoleEnum::SUPER_ADMIN->value,
            'guard_name' => 'admin',
        ]);
    }

    private function storeManagerRole(): Role
    {
        return Role::create([
            'name' => RoleEnum::MANAGER->value,
            'guard_name' => 'admin',
        ]);
    }
}
