<?php

namespace Modules\Category\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Admin\Enums\Role;

class CategoryStoreRequest extends FormRequest
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
                'tenant_id' => session('admin_tenant_id')
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'title_ar' => ['required', 'string', 'max:255'],
            'title_en' => ['required', 'string', 'max:255'],
            'publisher_id' => ['required', 'integer', 'exists:publishers,id'],
            'tenant_id' => ['required', 'string', 'exists:tenants,id'],
            'is_active' => ['nullable', 'boolean'],
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
