<?php

namespace Modules\Country\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Admin\Enums\Role;
use Modules\Country\Models\Country;
use Modules\Country\Models\Region;
use Spatie\Translatable\HasTranslations;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class City extends Model
{
    use BelongsToTenant, HasFactory, HasTranslations;

    protected $fillable = ['title', 'country_id', 'tenant_id', 'is_active'];

    public array $translatable = ['title'];

    //Date Serialization
    protected function serializeDate(\DateTimeInterface $date): string
    {
        return $date->format('Y-m-d H:i A');
    }

    //Scopes
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeByTenant(Builder $query): Builder
    {
        return $query->when(auth('admin')->check() && auth('admin')->user()->hasRole(Role::SUPER_ADMIN->value), function ($query) {
            $query->where('tenant_id', session('admin_tenant_id'));
        });
    }

    //Relations
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function regions(): HasMany
    {
        return $this->hasMany(Region::class);
    }

    public function zones(): HasMany
    {
        return $this->regions();
    }
}
