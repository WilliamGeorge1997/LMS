<?php

namespace Modules\School\DTOs;

use Modules\School\Http\Requests\SchoolStoreRequest;
use Modules\School\Http\Requests\SchoolUpdateRequest;

class SchoolDto
{
    public function __construct(
        public readonly string $title_ar,
        public readonly string $title_en,
        public readonly ?string $phone,
        public readonly ?string $email,
        public readonly int $country_id,
        public readonly int $city_id,
        public readonly int $region_id,
        public readonly ?string $tenant_id,
        public readonly int $is_active,
    ) {}

    public static function fromRequest(SchoolStoreRequest|SchoolUpdateRequest $request): self
    {
        return new self(
            title_ar: $request->input('title_ar'),
            title_en: $request->input('title_en'),
            phone: $request->input('phone'),
            email: $request->input('email'),
            country_id: (int) $request->input('country_id'),
            city_id: (int) $request->input('city_id'),
            region_id: (int) $request->input('region_id'),
            tenant_id: $request->input('tenant_id'),
            is_active: $request->has('is_active') ? 1 : 0,
        );
    }

    public function toArray(): array
    {
        $data = [
            'title' => [
                'ar' => $this->title_ar,
                'en' => $this->title_en,
            ],
            'phone' => $this->phone,
            'email' => $this->email,
            'country_id' => $this->country_id,
            'city_id' => $this->city_id,
            'region_id' => $this->region_id,
            'is_active' => $this->is_active,
        ];

        if (! is_null($this->tenant_id)) {
            $data['tenant_id'] = $this->tenant_id;
        }

        return $data;
    }
}
