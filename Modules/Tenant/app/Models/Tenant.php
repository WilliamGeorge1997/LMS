<?php

namespace Modules\Tenant\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Translatable\HasTranslations;
use Stancl\Tenancy\Database\Concerns\HasDomains;
use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;

class Tenant extends BaseTenant
{
    use HasDomains, HasFactory, HasTranslations;

    protected $fillable = ['id', 'name', 'is_active', 'data'];

    protected $translatable = ['name'];

    // Date Serialization
    protected function serializeDate(\DateTimeInterface $date): string
    {
        return $date->format('Y-m-d H:i A');
    }

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public static function getCustomColumns(): array
    {
        return ['id', 'name', 'is_active'];
    }

    // Scopes
    public function scopeActive(Builder $query)
    {
        return $query->where('is_active', 1);
    }
}
