<?php

namespace Modules\Country\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Country\Models\City;
use Spatie\Translatable\HasTranslations;

class Region extends Model
{
    use HasFactory, HasTranslations;

    protected $fillable = ['title', 'city_id', 'is_active'];

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

    //Relations
    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }
}
