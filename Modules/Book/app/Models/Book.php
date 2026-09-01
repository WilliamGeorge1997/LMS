<?php

namespace Modules\Book\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use Modules\Admin\Enums\Role;
use Modules\Book\Models\BookCode;
use Modules\Category\Models\Category;
use Modules\Level\Models\Level;
use Modules\Publisher\Models\Publisher;
use Spatie\Translatable\HasTranslations;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class Book extends Model
{
    use BelongsToTenant, HasFactory, HasTranslations;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'title',
        'isbn',
        'description',
        'publisher_id',
        'category_id',
        'level_id',
        'tenant_id',
        'is_active',
        'cover',
        'path',
    ];

    protected $translatable = ['title', 'description'];

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
        return $query->when(auth('admin')->user()->hasRole(Role::SUPER_ADMIN->value), function ($query) {
            $query->where('tenant_id', session('admin_tenant_id'));
        });
    }

    public function getCoverAttribute(?string $value): ?string
    {
        if (empty($value)) {
            return null;
        }

        $tenantPath = $this->tenant_id ? $this->tenant_id . '/' : 'central/';

        return Storage::disk('public')->url('uploads/' . $tenantPath . 'cover/' . $value);
    }

    public function getPathAttribute(?string $value): ?string
    {
        if (empty($value)) {
            return null;
        }

        $tenantPath = $this->tenant_id ? $this->tenant_id . '/' : 'central/';
        
        return Storage::disk('public')->url('uploads/' . $tenantPath . 'books/' . $value . '/index.html');
    }


    public function publisher(): BelongsTo
    {
        return $this->belongsTo(Publisher::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function level(): BelongsTo
    {
        return $this->belongsTo(Level::class);
    }

    public function bookCodes(): HasMany
    {
        return $this->hasMany(BookCode::class);
    }
}
