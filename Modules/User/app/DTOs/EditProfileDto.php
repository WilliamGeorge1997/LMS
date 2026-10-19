<?php

namespace Modules\User\DTOs;

use Illuminate\Support\Facades\Hash;
use Modules\User\Http\Requests\Web\EditProfileRequest;

readonly class EditProfileDto
{
    public function __construct(
        public string $name,
        public string $username,
        public ?string $password = null,
    ) {}

    public static function fromRequest(EditProfileRequest $request): self
    {
        return new self(
            name: $request->validated('name'),
            username: $request->validated('username'),
            password: $request->filled('new_password') ? $request->validated('new_password') : null,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = [
            'name' => $this->name,
            'username' => $this->username,
        ];

        if ($this->password !== null && $this->password !== '') {
            $data['password'] = Hash::make($this->password);
        }

        return $data;
    }
}
