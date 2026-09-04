<?php

namespace Modules\School\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Admin\Enums\Role;
use Modules\Book\Models\BookCode;
use Modules\Country\Models\City;
use Modules\Country\Models\Country;
use Modules\Country\Models\Region;
use Modules\Tenant\Models\Tenant;
use Modules\User\Models\User;
use Spatie\Translatable\HasTranslations;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class School extends Model
{
    use BelongsToTenant, HasFactory, HasTranslations;

    protected $fillable = [
        'title',
        'phone',
        'email',
        'country_id',
        'city_id',
        'region_id',
        'tenant_id',
        'is_active',
    ];

    public array $translatable = ['title'];

    protected function serializeDate(\DateTimeInterface $date): string
    {
        return $date->format('Y-m-d H:i A');
    }

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

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function bookCodes(): HasMany
    {
        return $this->hasMany(BookCode::class);
    }
}
