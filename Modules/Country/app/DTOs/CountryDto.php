<?php

namespace Modules\Country\DTOs;

use Illuminate\Http\Request;

class CountryDto
{
    /**
     * @param  array{en: string, ar: string}  $title
     */
    public function __construct(
        public readonly array $title,
        public readonly int $is_active,
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            title: [
                'en' => (string) $request->input('title.en'),
                'ar' => (string) $request->input('title.ar'),
            ],
            is_active: $request->has('is_active') ? 1 : 0,
        );
    }

    /**
     * @return array{title: array{en: string, ar: string}, is_active: int}
     */
    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'is_active' => $this->is_active,
        ];
    }
}
