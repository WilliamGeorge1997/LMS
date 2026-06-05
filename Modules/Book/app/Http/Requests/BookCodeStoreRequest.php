<?php

namespace Modules\Book\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Admin\Enums\Role;
use Modules\Book\Enums\BookCodeType;
use Modules\Book\Models\Book;

class BookCodeStoreRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $admin = auth('admin')->user();

        if (! $admin->hasRole(Role::SUPER_ADMIN->value)) {
            $this->merge([
                'tenant_id' => $admin->tenant_id,
            ]);
        }

        if ($admin->hasRole(Role::SUPER_ADMIN->value)) {
            $this->merge([
                'tenant_id' => session('admin_tenant_id'),
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'book_id' => [
                'required',
                'integer',
                Rule::exists(Book::class, 'id')->where('tenant_id', $this->input('tenant_id')),
            ],
            'duration' => ['required', 'integer', 'min:1', 'max:120'],
            'type' => ['required', Rule::enum(BookCodeType::class)],
            'quantity' => ['nullable', 'integer', 'min:1', 'max:500'],
            'tenant_id' => ['required', 'string', 'exists:tenants,id'],
        ];
    }

    public function attributes(): array
    {
        return [
            'tenant_id' => 'tenant',
            'book_id' => __('book::attributes.book'),
            'duration' => __('book::attributes.duration'),
            'type' => __('book::attributes.type'),
            'quantity' => __('book::attributes.quantity'),
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
