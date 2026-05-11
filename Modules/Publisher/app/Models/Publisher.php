<?php

namespace Modules\Publisher\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Admin\Enums\Role;
use Modules\Admin\Models\Admin;

class Publisher extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['name', 'manager_id', 'is_active'];

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

    // Relations
    public function manager(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'manager_id');
    }

    public function scopeAvailable(Builder $query): Builder
    {
        if (auth('admin')->check()) {
            $admin = auth('admin')->user();
            if ($admin->hasRole(Role::MANAGER)) {
                return $query->whereBelongsTo($admin, 'manager');
            }
        }

        return $query;
    }
}
