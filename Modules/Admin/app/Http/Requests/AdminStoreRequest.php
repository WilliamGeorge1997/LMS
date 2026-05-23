<?php

namespace Modules\Admin\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Admin\Enums\Role;

class AdminStoreRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $role = $this->input('role');

        if ($role === Role::MANAGER->value) {
            $this->merge(['tenant_id' => session('admin_tenant_id')]);
        }

        if ($role === Role::SUPER_ADMIN->value) {
            $this->merge(['tenant_id' => null]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:admins,email',
            'password' => 'required|string|min:6',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:1024',
            'is_active' => 'nullable|boolean',
            'role' => ['required', 'string', Rule::in(Role::values())],
        ];

        if ($this->input('role') === Role::MANAGER->value) {
            $rules['tenant_id'] = ['required', 'string', 'exists:tenants,id'];
        }

        if ($this->input('role') === Role::SUPER_ADMIN->value) {
            $rules['tenant_id'] = ['nullable'];
        }

        return $rules;
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'tenant_id.required' => __('admin::messages.tenant_context_required'),
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
