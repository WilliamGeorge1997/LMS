<?php

namespace Modules\Category\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Publisher\Models\Publisher;
use Spatie\Translatable\HasTranslations;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class Category extends Model
{
    use HasFactory, HasTranslations, BelongsToTenant;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['title','publisher_id', 'tenant_id', 'is_active'];

    protected $translatable = ['title'];
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
    public function publisher(): BelongsTo
    {
        return $this->belongsTo(Publisher::class);
    }
}
