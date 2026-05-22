<?php

namespace Modules\Publisher\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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
}
