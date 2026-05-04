<?php

namespace Modules\Country\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;
use Modules\Country\Models\City;

class RegionUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
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
            'country_id' => ['required', 'integer', Rule::exists('countries', 'id')],
            'city_id' => ['required', 'integer', Rule::exists('cities', 'id')],
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
                ->exists();

            if (! $ok) {
                $validator->errors()->add('city_id', __('The selected city does not belong to the selected country.'));
            }
        });
    }
}
