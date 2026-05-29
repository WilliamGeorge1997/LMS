<?php

namespace Modules\Book\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Book\Models\Book;

class BookUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        /** @var Book $book */
        $book = $this->route('book');

        return [
            'title_ar' => ['required', 'string', 'max:255'],
            'title_en' => ['required', 'string', 'max:255'],
            'isbn' => ['required', 'string', 'max:255', Rule::unique('books', 'isbn')->ignore($book->id)],
            'description_ar' => ['nullable', 'string'],
            'description_en' => ['nullable', 'string'],
            'publisher_id' => ['required', 'integer', 'exists:publishers,id'],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'level_id' => ['required', 'integer', 'exists:levels,id'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
