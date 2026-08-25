<?php

namespace Modules\User\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rules\Enum;
use Modules\User\Enums\UserType;
use Override;

class UserRegisterRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'username' => ['required', 'string', 'max:255', 'unique:users,username'],
            'password' => ['required', 'string'],
            'type' => ['required', new Enum(UserType::class)],
            'code' => ['required', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp,gif', 'max:2048'],
            'school_id' => ['required', 'exists:schools,id'],
            'country_id' => ['required', 'exists:countries,id'],
            'city_id' => ['required', 'exists:cities,id'],
            'region_id' => ['required', 'exists:regions,id'],
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     */
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
            'image' => __('user::attributes.image'),
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
            'email.unique'    => __('user::message.email_taken'),
            'username.unique' => __('user::message.username_taken'),
        ];
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            apiResponse(false, 'Validation errors', $validator->errors()->toArray(), 'validation_error')
        );
    }
}
