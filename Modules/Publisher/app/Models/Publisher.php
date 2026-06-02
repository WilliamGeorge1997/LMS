<?php

namespace Modules\Publisher\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Admin\Enums\Role;
use Spatie\Translatable\HasTranslations;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class Publisher extends Model
{
    use HasFactory, HasTranslations, BelongsToTenant;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['name', 'tenant_id', 'is_active'];

    protected $translatable = ['name'];

    // Date Serialization
    protected function serializeDate(\DateTimeInterface $date): string
    {
        return $date->format('Y-m-d H:i A');
    }

    // Scopes
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeByTenant(Builder $query): Builder
    {
        //Scope for super admin
        return $query->when(auth('admin')->user()->hasRole(Role::SUPER_ADMIN->value), function ($query) {
            $query->where('tenant_id', session('admin_tenant_id'));
        });

        //Already scoped for tenant by BelongsToTenant trait
    }
}
