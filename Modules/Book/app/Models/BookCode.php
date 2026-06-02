<?php

namespace Modules\Book\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Admin\Enums\Role;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class BookCode extends Model
{
    use BelongsToTenant, HasFactory;

    protected $fillable = [
        'book_id',
        'tenant_id',
        'code',
        'duration',
        'type',
        'is_active',
        'is_used',
        'from',
        'to',
    ];

    protected function serializeDate(\DateTimeInterface $date): string
    {
        return $date->format('Y-m-d');
    }

    public function scopeByTenant(Builder $query): Builder
    {
        return $query->when(auth('admin')->user()?->hasRole(Role::SUPER_ADMIN->value), function (Builder $query) {
            $query->where('tenant_id', session('admin_tenant_id'));
        });
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }
}
