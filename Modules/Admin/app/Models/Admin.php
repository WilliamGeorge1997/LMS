<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Admin extends Model
{
    use HasFactory;

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
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    //Accessors
    public function getImageAttribute($value)
    {
        if (empty($value)) {
            return $value;
        }
        if (filter_var($value, FILTER_VALIDATE_URL)) {
            return $value;
        }
        return asset('uploads/admin/' . $value);
    }
}
