<?php

namespace Modules\User\Http\Requests\Web;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
use Modules\User\Enums\UserType;
use Override;

class UserLoginRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
            'type' => ['required', new Enum(UserType::class)],
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
            'type' => __('user::attributes.type'),
            'remember' => __('user::attributes.remember'),
        ];
    }

    #[Override]
    public function messages(): array
    {
        return [
            'login.required' => __('validation.required', ['attribute' => __('user::attributes.login')]),
            'password.required' => __('validation.required', ['attribute' => __('user::attributes.password')]),
            'type.required' => __('validation.required', ['attribute' => __('user::attributes.type')]),
        ];
    }
}
