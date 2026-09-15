<?php

namespace Modules\Book\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Admin\Enums\Role;

class BookCodeExportRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $admin = auth('admin')->user();

        if (! $admin->hasRole(Role::SUPER_ADMIN->value)) {
            $this->merge([
                'tenant_id' => $admin->tenant_id,
            ]);
        }

        if ($admin->hasRole(Role::SUPER_ADMIN->value)) {
            $this->merge([
                'tenant_id' => session('admin_tenant_id'),
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'tenant_id' => ['required', 'string', 'exists:tenants,id'],
        ];
    }

    public function attributes(): array
    {
        return [
            'tenant_id' => 'tenant',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
