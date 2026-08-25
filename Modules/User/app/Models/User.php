<?php

namespace Modules\User\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Modules\Book\Models\Book;
use Modules\Book\Models\BookCode;
use Modules\Country\Models\City;
use Modules\Country\Models\Country;
use Modules\Country\Models\Region;
use Modules\School\Models\School;
use Modules\User\Enums\UserType;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'type',
        'name',
        'email',
        'username',
        'password',
        'image',
        'verify_code',
        'school_id',
        'country_id',
        'city_id',
        'region_id',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'verify_code'
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'type' => UserType::class,
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Prepare a date for array / JSON serialization.
     */
    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format('Y-m-d H:i:s');
    }

    /**
     * Scope a query to only include active users.
     */
    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', true);
    }
    
    /**
     * Scope a query to only include teachers.
     */
    public function scopeTeachers(Builder $query): void
    {
        $query->where('type', UserType::TEACHER);
    }

    /**
     * Scope a query to only include students.
     */
    public function scopeStudents(Builder $query): void
    {
        $query->where('type', UserType::STUDENT);
    }

    /**
     * Check if user is a teacher.
     */
    public function isTeacher(): bool
    {
        return $this->type === UserType::TEACHER;
    }

    /**
     * Check if user is a student.
     */
    public function isStudent(): bool
    {
        return $this->type === UserType::STUDENT;
    }

    /**
     * Get the school that owns the user.
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Get the country that owns the user.
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * Get the city that owns the user.
     */
    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    /**
     * Get the region that owns the user.
     */
    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }

    /**
     * Get the book codes assigned to the user.
     */
    public function bookCodes(): HasMany
    {
        return $this->hasMany(BookCode::class);
    }

    /**
     * Get the books assigned to the user via access codes.
     */
    public function books(): BelongsToMany
    {
        return $this->belongsToMany(Book::class, 'book_codes');
    }
}
