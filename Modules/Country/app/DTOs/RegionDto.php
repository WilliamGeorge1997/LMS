<?php

namespace Modules\Country\DTOs;

use Illuminate\Http\Request;

class RegionDto
{
    /**
     * @param  array{en: string, ar: string}  $title
     */
    public function __construct(
        public readonly int $city_id,
        public readonly array $title,
        public readonly int $is_active,
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            city_id: (int) $request->input('city_id'),
            title: [
                'en' => (string) $request->input('title.en'),
                'ar' => (string) $request->input('title.ar'),
            ],
            is_active: $request->has('is_active') ? 1 : 0,
        );
    }

    /**
     * @return array{city_id: int, title: array{en: string, ar: string}, is_active: int}
     */
    public function toArray(): array
    {
        return [
            'city_id' => $this->city_id,
            'title' => $this->title,
            'is_active' => $this->is_active,
        ];
    }
}
