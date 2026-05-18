<?php

namespace Modules\Tenant\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;
use Stancl\Tenancy\Database\Concerns\HasDomains;
use Spatie\Translatable\HasTranslations;

class Tenant extends BaseTenant
{
    use HasFactory, HasDomains, HasTranslations;

    protected $fillable = ['id', 'name', 'is_active', 'data'];

    protected $translatable = ['name'];

    protected $appends = ['name_ar', 'name_en', 'domain'];

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

    public function getNameArAttribute(): string
    {
        return $this->getTranslation('name', 'ar');
    }

    public function getNameEnAttribute(): string
    {
        return $this->getTranslation('name', 'en');
    }

    public function getDomainAttribute(): string
    {
        return $this->domains->first()?->domain ?? '';
    }
}
