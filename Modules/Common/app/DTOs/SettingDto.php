<?php

namespace Modules\Common\DTOs;

use Modules\Common\Http\Requests\SettingRequest;

readonly class SettingDto
{
    public function __construct(
        public ?string $app_version,
        public mixed $app_path,
    ) {}

    public static function fromRequest(SettingRequest $request): self
    {
        return new self(
            app_version: $request->validated('app_version'),
            app_path: $request->validated('app_path'),
        );
    }

    public function toArray(): array
    {
        return [
            'app_version' => $this->app_version,
            'app_path' => $this->app_path,
        ];
    }
}
