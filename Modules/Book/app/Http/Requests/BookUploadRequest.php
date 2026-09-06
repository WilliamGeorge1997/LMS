<?php

namespace Modules\Book\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Admin\Enums\Role;

class BookUploadRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth('admin')->check() && auth('admin')->user()->hasAnyRole([Role::SUPER_ADMIN->value, Role::MANAGER->value]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'file' => ['nullable'],
        ];
    }
}
