<?php

namespace Modules\Country\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;
use Modules\Admin\Enums\Role;
use Modules\Country\Models\City;

class RegionStoreRequest extends FormRequest
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
            'city_id' => [
                'required',
                'integer',
                Rule::exists('cities', 'id')->where(function ($query) {
                    $query->where('country_id', $this->input('country_id'))
                        ->where('tenant_id', $this->input('tenant_id'));
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

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if (! $this->filled('city_id') || ! $this->filled('country_id')) {
                return;
            }

            $ok = City::query()
                ->whereKey((int) $this->input('city_id'))
                ->where('country_id', (int) $this->input('country_id'))
                ->where('tenant_id', $this->input('tenant_id'))
                ->exists();

            if (! $ok) {
                $validator->errors()->add('city_id', __('The selected city does not belong to the selected country.'));
            }
        });
    }

    public function attributes(): array
    {
        return [
            'tenant_id' => __('country::attributes.tenant'),
            'country_id' => __('country::attributes.country_id'),
            'city_id' => __('country::attributes.city_id'),
        ];
    }
}
