<?php

namespace Modules\Category\DTOs;

use Modules\Category\Http\Requests\CategoryRequest;

class CategoryDto
{
    public function __construct(
        public readonly string $name_ar,
        public readonly string $name_en,
        public readonly int $publisher_id,
        public readonly int $manager_id,
        public readonly bool $is_active,
    ) {
    }

    public static function fromRequest(CategoryRequest $request): self
    {
        return new self(
            name_ar: $request->input('name_ar'),
            name_en: $request->input('name_en'),
            publisher_id: $request->input('publisher_id'),
            manager_id: $request->input('manager_id'),
            is_active: $request->has('is_active') ? 1 : 0,
        );
    }

    public function toArray(): array
    {
        return [
            'name' => [
                'ar' => $this->name_ar,
                'en' => $this->name_en,
            ],
            'publisher_id' => $this->publisher_id,
            'manager_id' => $this->manager_id,
            'is_active' => $this->is_active,
        ];
    }
}