<?php

namespace Modules\Country\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Country\Models\City;
use Spatie\Translatable\HasTranslations;

class Country extends Model
{
    use HasFactory, HasTranslations;

    protected $fillable = ['title', 'is_active'];

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
    public function cities(): HasMany
    {
        return $this->hasMany(City::class);
    }
}
