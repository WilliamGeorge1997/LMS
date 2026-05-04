<?php

namespace Modules\Country\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CityUpdateRequest extends FormRequest
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
            'title.en' => 'required|string|max:255',
            'title.ar' => 'required|string|max:255',
            'is_active' => 'nullable|boolean',
            'title_en' => 'nullable',
            'title_ar' => 'nullable',
        ];
    }
}
