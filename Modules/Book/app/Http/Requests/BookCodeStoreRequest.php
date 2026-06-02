<?php

namespace Modules\Book\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Book\Enums\BookCodeType;
use Modules\Book\Models\Book;

class BookCodeStoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'book_id' => [
                'required',
                'integer',
                Rule::exists(Book::class, 'id')->where(function ($query) {
                    $query->byTenant();
                }),
            ],
            'duration' => ['required', 'integer', 'min:1', 'max:120'],
            'type' => ['required', Rule::enum(BookCodeType::class)],
            'quantity' => ['nullable', 'integer', 'min:1', 'max:500'],
        ];
    }

    public function attributes(): array
    {
        return [
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
