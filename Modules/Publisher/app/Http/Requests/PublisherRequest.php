<?php

namespace Modules\Publisher\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Admin\Enums\Role;

class PublisherRequest extends FormRequest
{
    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $admin = auth('admin')->user();

        if (! $admin->hasRole(Role::SUPER_ADMIN->value)) {
            $this->merge([
                'manager_id' => $admin->getKey(),
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
            'manager_id' => ['required', 'integer', 'exists:admins,id'],
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
