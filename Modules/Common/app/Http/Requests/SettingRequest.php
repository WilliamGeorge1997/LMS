<?php

namespace Modules\Common\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SettingRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'app_version' => ['nullable', 'string', 'max:255'],
            'app_path' => ['nullable', 'file'],
            'mail_email' => ['nullable', 'email', 'max:255'],
            'mail_password' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
}
