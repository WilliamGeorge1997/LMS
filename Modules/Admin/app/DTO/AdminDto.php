<?php

namespace Modules\Admin\DTO;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminDto
{
    public string $name;
    public string $email;
    public string $password;
    public int $is_active;
    public string $role;

    public function __construct(Request $request)
    {
        $this->name = $request->input('name');
        $this->email = $request->input('email');
        $this->password = Hash::make($request->input('password'));
        $this->is_active = $request->has('is_active') ? 1 : 0;
        $this->role = $request->input('role');
    }

    public function dataFromRequest(): array
    {
        $data = json_decode(json_encode($this), true);
        return array_filter($data, fn($value) => !is_null($value));
    }
}
