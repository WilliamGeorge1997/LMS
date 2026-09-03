<?php

namespace Modules\Book\Http\Requests\Api;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Override;

class RedeemCodeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'code' => ['required', 'string'],
        ];
    }

    #[Override]
    public function attributes(): array
    {
        return [
            'code' => __('book::attributes.access_code'),
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            apiResponse(false, 'Validation errors', $validator->errors()->toArray(), 'validation_error')
        );
    }
}
