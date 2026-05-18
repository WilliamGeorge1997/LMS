<?php

namespace Modules\Tenant\DTOs;

use Modules\Tenant\Http\Requests\TenantRequest;

class TenantDto
{
    public function __construct(
        public readonly string $name_ar,
        public readonly string $name_en,
        public readonly string $domain,
        public readonly bool $is_active,
    ) {
    }

    public static function fromRequest(TenantRequest $request): self
    {
        return new self(
            name_ar: $request->input('name_ar'),
            name_en: $request->input('name_en'),
            domain: $request->input('domain'),
            is_active: $request->has('is_active'),
        );
    }

    /**
     * @return array{name: array{ar: string, en: string}, is_active: bool}
     */
    public function toArray(): array
    {
        return [
            'name' => [
                'ar' => $this->name_ar,
                'en' => $this->name_en,
            ],
            'is_active' => $this->is_active,
        ];
    }
}
