<?php

namespace Modules\Tenant\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Tenant\Models\Tenant;

class TenantRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        if ($this->filled('domain')) {
            $this->merge([
                'domain' => strtolower((string) $this->input('domain')),
            ]);
        }
    }

    public function rules(): array
    {
        /** @var Tenant|null $tenant */
        $tenant = $this->route('tenant');
        $domainId = $tenant?->domains()->value('id');

        return [
            'name_ar' => ['required', 'string', 'max:255'],
            'name_en' => ['required', 'string', 'max:255'],
            'domain' => [
                'required',
                'string',
                'max:63',
                'regex:/^[a-z0-9](?:[a-z0-9-]*[a-z0-9])?$/',
                Rule::unique('domains', 'domain')->ignore($domainId),
            ],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'domain' => __('tenant::attributes.domain'),
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
