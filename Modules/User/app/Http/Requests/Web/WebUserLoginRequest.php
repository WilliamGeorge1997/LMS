<?php

namespace Modules\User\Http\Requests\Web;

use Illuminate\Foundation\Http\FormRequest;

class WebUserLoginRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
