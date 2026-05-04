<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Traits\HasRoles;

class Admin extends Model
{
    use HasFactory, HasRoles;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['name', 'email', 'password', 'image', 'is_active'];
    protected $hidden = ['password', 'remember_token'];

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

    //Accessors
    public function getImageAttribute(?string $value): ?string
    {
        if (empty($value)) {
            return null;
        }

        return Storage::url('uploads/admin/' . $value);
    }
     
}
