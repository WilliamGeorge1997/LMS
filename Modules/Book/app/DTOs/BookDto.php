<?php

namespace Modules\Book\DTOs;

use Illuminate\Http\UploadedFile;
use Modules\Book\Http\Requests\BookStoreRequest;
use Modules\Book\Http\Requests\BookUpdateRequest;

class BookDto
{
    public function __construct(
        public readonly string $title_ar,
        public readonly string $title_en,
        public readonly string $isbn,
        public readonly ?string $description_ar,
        public readonly ?string $description_en,
        public readonly int $publisher_id,
        public readonly int $category_id,
        public readonly int $level_id,
        public readonly ?string $tenant_id,
        public readonly bool $is_active,
        public readonly ?UploadedFile $cover = null,
    ) {}

    public static function fromRequest(BookStoreRequest|BookUpdateRequest $request): self
    {
        return new self(
            title_ar: $request->input('title_ar'),
            title_en: $request->input('title_en'),
            isbn: $request->input('isbn'),
            description_ar: $request->input('description_ar'),
            description_en: $request->input('description_en'),
            publisher_id: $request->input('publisher_id'),
            category_id: $request->input('category_id'),
            level_id: $request->input('level_id'),
            tenant_id: $request->input('tenant_id'),
            is_active: $request->has('is_active') ? 1 : 0,
            cover: $request->file('cover'),
        );
    }

    public function toArray(): array
    {
        $data = [
            'title' => [
                'ar' => $this->title_ar,
                'en' => $this->title_en,
            ],
            'isbn' => $this->isbn,
            'description' => [
                'ar' => $this->description_ar,
                'en' => $this->description_en,
            ],
            'publisher_id' => $this->publisher_id,
            'category_id' => $this->category_id,
            'level_id' => $this->level_id,
            'is_active' => $this->is_active,
        ];

        if (! is_null($this->tenant_id)) {
            $data['tenant_id'] = $this->tenant_id;
        }

        if (is_null($this->cover)) {
            unset($data['cover']);
        }

        return $data;
    }
}
