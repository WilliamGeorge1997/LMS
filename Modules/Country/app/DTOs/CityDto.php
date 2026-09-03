<?php

namespace Modules\Country\DTOs;

use Illuminate\Http\Request;

class CityDto
{
    /**
     * @param  array{en: string, ar: string}  $title
     */
    public function __construct(
        public readonly int $country_id,
        public readonly array $title,
        public readonly ?string $tenant_id,
        public readonly int $is_active,
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            country_id: (int) $request->input('country_id'),
            title: [
                'en' => (string) $request->input('title.en'),
                'ar' => (string) $request->input('title.ar'),
            ],
            tenant_id: $request->input('tenant_id'),
            is_active: $request->has('is_active') ? 1 : 0,
        );
    }

    /**
     * @return array{country_id: int, title: array{en: string, ar: string}, tenant_id?: string, is_active: int}
     */
    public function toArray(): array
    {
        $data = [
            'country_id' => $this->country_id,
            'title' => $this->title,
            'is_active' => $this->is_active,
        ];

        if (! is_null($this->tenant_id)) {
            $data['tenant_id'] = $this->tenant_id;
        }

        return $data;
    }
}
