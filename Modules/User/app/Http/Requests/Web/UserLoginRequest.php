<?php

namespace Modules\User\Http\Requests\Web;

use Illuminate\Foundation\Http\FormRequest;
use Override;

class UserLoginRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
            'remember' => ['nullable', 'boolean'],
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
            'login' => __('user::attributes.login'),
            'password' => __('user::attributes.password'),
            'remember' => __('user::attributes.remember'),
        ];
    }

    #[Override]
    public function messages(): array
    {
        return [
            'login.required' => __('validation.required', ['attribute' => __('user::attributes.login')]),
            'password.required' => __('validation.required', ['attribute' => __('user::attributes.password')]),
        ];
    }
}
