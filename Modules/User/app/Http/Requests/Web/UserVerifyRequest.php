<?php

namespace Modules\User\Http\Requests\Web;

use Illuminate\Foundation\Http\FormRequest;
use Override;

class UserVerifyRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'otp' => ['required', 'string', 'size:6'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }

    #[Override]
    public function attributes(): array
    {
        return [
            'otp' => __('user::attributes.verify_code'),
        ];
    }

    #[Override]
    public function messages(): array
    {
        return [
            'otp.required' => __('user::message.wrong_otp'),
            'otp.size' => __('user::message.wrong_otp'),
        ];
    }
}
