<?php

namespace Modules\Country\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Admin\Enums\Role;

class CityUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $admin = auth('admin')->user();

        if ($admin && ! $admin->hasRole(Role::SUPER_ADMIN->value)) {
            $this->merge([
                'tenant_id' => $admin->tenant_id,
            ]);
        }

        if ($admin && $admin->hasRole(Role::SUPER_ADMIN->value)) {
            $this->merge([
                'tenant_id' => session('admin_tenant_id'),
            ]);
        }

        $this->merge([
            'title' => [
                'en' => $this->input('title_en'),
                'ar' => $this->input('title_ar'),
            ],
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'country_id' => [
                'required',
                'integer',
                Rule::exists('countries', 'id')->where(function ($query) {
                    $query->where('tenant_id', $this->input('tenant_id'));
                }),
            ],
            'tenant_id' => ['required', 'string', 'exists:tenants,id'],
            'title.en' => 'required|string|max:255',
            'title.ar' => 'required|string|max:255',
            'is_active' => 'nullable|boolean',
            'title_en' => 'nullable',
            'title_ar' => 'nullable',
        ];
    }

    public function attributes(): array
    {
        return [
            'tenant_id' => __('country::attributes.tenant'),
            'country_id' => __('country::attributes.country_id'),
        ];
    }
}
