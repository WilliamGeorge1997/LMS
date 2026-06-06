<?php

namespace Modules\School\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\School\Models\School;

class SchoolUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        /** @var School $school */
        $school = $this->route('school');

        return [
            'title_ar' => ['required', 'string', 'max:255'],
            'title_en' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'string', 'email', 'max:255', Rule::unique('schools', 'email')->ignore($school->id)],
            'country_id' => ['required', 'integer', 'exists:countries,id'],
            'city_id' => [
                'required',
                'integer',
                Rule::exists('cities', 'id')->where(function ($query) {
                    $query->where('country_id', $this->input('country_id'));
                }),
            ],
            'region_id' => [
                'required',
                'integer',
                Rule::exists('regions', 'id')->where(function ($query) {
                    $query->where('city_id', $this->input('city_id'));
                }),
            ],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'city_id.exists' => __('school::validations.city_country_mismatch'),
            'region_id.exists' => __('school::validations.region_city_mismatch'),
        ];
    }

    public function attributes(): array
    {
        return [
            'title_ar' => __('school::attributes.title_ar'),
            'title_en' => __('school::attributes.title_en'),
            'country_id' => __('school::attributes.country_id'),
            'city_id' => __('school::attributes.city_id'),
            'region_id' => __('school::attributes.region_id'),
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
