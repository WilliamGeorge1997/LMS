<?php

namespace Modules\Category\DTOs;

use Modules\Category\Http\Requests\CategoryStoreRequest;
use Modules\Category\Http\Requests\CategoryUpdateRequest;


class CategoryDto
{
    public function __construct(
        public readonly string $title_ar,
        public readonly string $title_en,
        public readonly int $publisher_id,
        public readonly ?string $tenant_id,
        public readonly bool $is_active,
    ) {}

    public static function fromRequest(CategoryStoreRequest|CategoryUpdateRequest $request): self
    {
        return new self(
            title_ar: $request->input('title_ar'),
            title_en: $request->input('title_en'),
            publisher_id: $request->input('publisher_id'),
            tenant_id: $request->input('tenant_id'),
            is_active: $request->has('is_active') ? 1 : 0,
        );
    }

    public function toArray(): array
    {
        $data =  [
            'title' => [
                'ar' => $this->title_ar,
                'en' => $this->title_en,
            ],
            'publisher_id' => $this->publisher_id,
            'is_active' => $this->is_active,
        ];

        if (! is_null($this->tenant_id)) {
            $data['tenant_id'] = $this->tenant_id;
        }

        return $data;
    }
}
