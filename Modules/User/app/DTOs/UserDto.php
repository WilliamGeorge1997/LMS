<?php

namespace Modules\User\DTOs;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Modules\User\Http\Requests\UserRegisterRequest;

readonly class UserDto
{
    public function __construct(
        public string $name,
        public string $email,
        public string $username,
        public string $password,
        public string $type,
        public string $code,
        public int $schoolId,
        public int $countryId,
        public int $cityId,
        public int $regionId,
        public ?UploadedFile $image = null,
        public ?string $verifyCode = null,
    ) {}

    public static function fromRequest(UserRegisterRequest $request): self
    {
        return new self(
            name: $request->validated('name'),
            email: $request->validated('email'),
            username: $request->validated('username'),
            password: $request->validated('password'),
            type: $request->validated('type'),
            code: $request->validated('code'),
            schoolId: (int) $request->validated('school_id'),
            countryId: (int) $request->validated('country_id'),
            cityId: (int) $request->validated('city_id'),
            regionId: (int) $request->validated('region_id'),
            image: $request->file('image'),
            verifyCode: (string) random_int(100000, 999999),
        );
    }
    
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'username' => $this->username,
            'password' => Hash::make($this->password),
            'type' => $this->type,
            'school_id' => $this->schoolId,
            'country_id' => $this->countryId,
            'city_id' => $this->cityId,
            'region_id' => $this->regionId,
            'verify_code' => $this->verifyCode,
        ];
    }
}
