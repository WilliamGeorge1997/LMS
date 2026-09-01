<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticable;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Traits\HasRoles;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class Admin extends Authenticable
{
    use HasFactory, HasRoles, BelongsToTenant;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['name', 'email', 'password', 'image', 'tenant_id', 'is_active'];

    protected $hidden = ['password', 'remember_token'];

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

    // Accessors
    public function getImageAttribute(?string $value): ?string
    {
        if (empty($value)) {
            return null;
        }

        $tenantPath = $this->tenant_id ? $this->tenant_id . '/' : 'central/';
        
        return Storage::disk('public')->url('uploads/' . $tenantPath . 'admin/' . $value);
    }
}
