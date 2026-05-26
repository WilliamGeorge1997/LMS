<?php

namespace Modules\Publisher\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Admin\Enums\Role;

class PublisherStoreRequest extends FormRequest
{
    /**
     * Prepare the data for validation.
     */
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

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name_ar' => ['required', 'string', 'max:255'],
            'name_en' => ['required', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
            'tenant_id' => ['required', 'string', 'exists:tenants,id'],
        ];
    }

    public function attributes(): array
    {
        return [
            'tenant_id' => 'tenant',
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
}
