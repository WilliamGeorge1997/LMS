<?php

namespace Modules\User\DTOs;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;
use Modules\User\Http\Requests\EditProfileRequest;
use Modules\User\Http\Requests\UserRegisterRequest;

readonly class UserDto
{
    public function __construct(
        public string $name,
        public string $email,
        public string $username,
        public ?int $schoolId,
        public int $countryId,
        public int $cityId,
        public int $regionId,
        public ?string $password = null,
        public ?string $type = null,
        public ?string $code = null,
        public ?string $verifyCode = null,
        public ?bool $isActive = null,
    ) {}

    public static function fromRegisterRequest(UserRegisterRequest $request): self
    {
        return self::buildFromRequest($request, true);
    }

    public static function fromEditProfileRequest(EditProfileRequest $request): self
    {
        return self::buildFromRequest($request, false);
    }

    private static function buildFromRequest(FormRequest $request, bool $isRegister): self
    {
        return new self(
            name: $request->validated('name'),
            email: $request->validated('email'),
            username: $request->validated('username'),
            schoolId: $request->validated('school_id'),
            countryId: (int) $request->validated('country_id'),
            cityId: (int) $request->validated('city_id'),
            regionId: (int) $request->validated('region_id'),
            password: $isRegister ? $request->validated('password') : $request->validated('new_password'),
            type: $isRegister ? $request->validated('type') : null,
            code: $isRegister ? $request->validated('code') : null,
            verifyCode: $isRegister ? (string) rand(100000, 999999) : null,
            isActive: $isRegister ? false : null,
        );
    }
    
    public function toArray(): array
    {
        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'username' => $this->username,
            'country_id' => $this->countryId,
            'city_id' => $this->cityId,
            'region_id' => $this->regionId,
        ];

        if ($this->schoolId !== null) {
            $data['school_id'] = $this->schoolId;
        }

        if ($this->password) {
            $data['password'] = Hash::make($this->password);
        }

        if ($this->type) {
            $data['type'] = $this->type;
        }

        if ($this->code) {
            $data['code'] = $this->code;
        }

        if ($this->verifyCode) {
            $data['verify_code'] = $this->verifyCode;
        }

        if ($this->isActive !== null) {
            $data['is_active'] = $this->isActive;
        }

        return $data;
    }
}
