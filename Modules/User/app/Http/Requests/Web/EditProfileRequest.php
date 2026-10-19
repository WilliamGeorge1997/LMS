<?php

namespace Modules\User\Http\Requests\Web;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Override;

class EditProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('user_web')->check();
    }

    public function rules(): array
    {
        $userId = auth('user_web')->id();

        return [
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', Rule::unique('users', 'username')->ignore($userId)],
            'old_password' => ['nullable', 'required_with:new_password', 'current_password:user_web'],
            'new_password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ];
    }

    #[Override]
    public function attributes(): array
    {
        return [
            'name' => __('user::attributes.name'),
            'username' => __('user::attributes.username'),
            'old_password' => __('user::attributes.old_password'),
            'new_password' => __('user::attributes.new_password'),
        ];
    }

    #[Override]
    public function messages(): array
    {
        return [
            'username.unique' => __('user::message.username_taken'),
        ];
    }
}
