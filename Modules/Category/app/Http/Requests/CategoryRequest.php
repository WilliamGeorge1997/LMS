<?php

namespace Modules\Category\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Admin\Enums\Role;

class CategoryRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $admin = auth('admin')->user();

        if (!$admin->hasRole(Role::SUPER_ADMIN->value)) {
            $this->merge([
                'manager_id' => $admin->getKey(),
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'name_ar' => ['required', 'string', 'max:255'],
            'name_en' => ['required', 'string', 'max:255'],
            'publisher_id' => ['required', 'integer', 'exists:publishers,id'],
            'manager_id' => ['required', 'integer', 'exists:admins,id'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}