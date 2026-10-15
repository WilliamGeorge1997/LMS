<?php

namespace Modules\Book\Http\Requests\Web;

use Illuminate\Foundation\Http\FormRequest;

class RedeemCodeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('user_web')->check();
    }

    public function rules(): array
    {
        return [
            'code' => ['required', 'string'],
        ];
    }

    public function attributes(): array
    {
        return [
            'code' => __('book::attributes.access_code'),
        ];
    }
}
