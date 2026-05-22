<?php

namespace Modules\Publisher\DTOs;

use Illuminate\Http\Request;

class PublisherDto
{
    public function __construct(
        public readonly string $name_ar,
        public readonly string $name_en,
        public readonly int $tenant_id,
        public readonly int $is_active,
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            name_ar: $request->input('name_ar'),
            name_en: $request->input('name_en'),
            tenant_id: $request->input('tenant_id'),
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
            'tenant_id' => $this->tenant_id,
            'is_active' => $this->is_active,
        ];
    }
}
