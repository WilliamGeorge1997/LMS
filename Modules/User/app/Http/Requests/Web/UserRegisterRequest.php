<?php

namespace Modules\User\Http\Requests\Web;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
use Modules\User\Enums\UserType;
use Override;

class UserRegisterRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        if (! $this->has('type') && session()->has('user_type')) {
            $this->merge([
                'type' => session('user_type'),
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'username' => ['required', 'string', 'max:255', 'unique:users,username'],
            'password' => ['required', 'string', 'confirmed'],
            'type' => ['required', new Enum(UserType::class)],
            'code' => ['required', 'string'],
            'school_id' => ['required', 'exists:schools,id'],
            'country_id' => ['required', 'exists:countries,id'],
            'city_id' => ['required', 'exists:cities,id'],
            'region_id' => ['required', 'exists:regions,id'],
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
            'name' => __('user::attributes.name'),
            'email' => __('user::attributes.email'),
            'username' => __('user::attributes.username'),
            'password' => __('user::attributes.password'),
            'type' => __('user::attributes.type'),
            'code' => __('user::attributes.code'),
            'school_id' => __('user::attributes.school_id'),
            'country_id' => __('user::attributes.country_id'),
            'city_id' => __('user::attributes.city_id'),
            'region_id' => __('user::attributes.region_id'),
        ];
    }

    #[Override]
    public function messages(): array
    {
        return [
            'email.unique' => __('user::message.email_taken'),
            'username.unique' => __('user::message.username_taken'),
        ];
    }
}
