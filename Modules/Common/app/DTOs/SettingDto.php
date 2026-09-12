<?php

namespace Modules\Common\DTOs;

use Modules\Common\Http\Requests\SettingRequest;

readonly class SettingDto
{
    public function __construct(
        public ?string $app_version,
        public mixed $app_path,
        public ?string $mail_email = null,
        public ?string $mail_password = null,
    ) {}

    public static function fromRequest(SettingRequest $request): self
    {
        return new self(
            app_version: $request->validated('app_version'),
            app_path: $request->validated('app_path'),
            mail_email: $request->validated('mail_email'),
            mail_password: $request->validated('mail_password'),
        );
    }

    public function toArray(): array
    {
        return [
            'app_version' => $this->app_version,
            'app_path' => $this->app_path,
            'mail_email' => $this->mail_email,
            'mail_password' => $this->mail_password,
        ];
    }
}
