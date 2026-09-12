<?php

namespace Modules\Common\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Modules\Tenant\Models\Tenant;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class Setting extends Model
{
    use BelongsToTenant, HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'tenant_id',
        'app_version',
        'app_path',
        'mail_email',
        'mail_password',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function getAppPathAttribute(?string $value): ?string
    {
        if (empty($value)) {
            return null;
        }

        $tenantPath = $this->tenant_id ? $this->tenant_id . '/' : 'central/';

        return Storage::disk('public')->url('uploads/' . $tenantPath . 'settings/app/' . $value);
    }
}
