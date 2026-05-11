<?php

namespace Modules\Publisher\DTOs;

use Illuminate\Http\Request;

class PublisherDto
{
    public function __construct(
        public readonly string $name,
        public readonly int $manager_id,
        public readonly int $is_active,
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            name: $request->input('name'),
            manager_id: $request->input('manager_id'),
            is_active: $request->has('is_active') ? 1 : 0,
        );
    }

    /**
     * @return array{name: string, manager_id: int, is_active: int}
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'manager_id' => $this->manager_id,
            'is_active' => $this->is_active,
        ];
    }
}
