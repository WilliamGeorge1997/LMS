<?php

namespace Modules\User\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Override;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\Rule;

class EditProfileRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $userId = auth('user')->id();
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($userId)],
            'username' => ['required', 'string', 'max:255', Rule::unique('users', 'username')->ignore($userId)],
            'old_password' => ['nullable', 'required_with:new_password', 'current_password:user'],
            'new_password' => ['nullable', 'string', 'confirmed'],
            'school_id' => ['prohibted'],
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
            'old_password' => __('user::attributes.old_password'),
            'new_password' => __('user::attributes.new_password'),
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
