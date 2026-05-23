<?php

namespace Modules\Admin\DTOs;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminDto
{
    public function __construct(
        public readonly string  $name,
        public readonly string  $email,
        public readonly int     $is_active,
        public readonly string  $role,
        public readonly ?string $tenant_id = null,
        public readonly ?string $password = null,
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            name: $request->input('name'),
            email: $request->input('email'),
            is_active: $request->has('is_active') ? 1 : 0,
            role: $request->input('role'),
            tenant_id: $request->input('tenant_id'),
            password: $request->filled('password') ? Hash::make($request->input('password')) : null,
        );
    }

    public function toArray(): array
    {
        $data = [
            'name'      => $this->name,
            'email'     => $this->email,
            'tenant_id' => $this->tenant_id,
            'is_active' => $this->is_active,
        ];

        if (! is_null($this->password)) {
            $data['password'] = $this->password;
        }

        return $data;
    }
}
