<?php

namespace Modules\User\Services;

use Modules\User\DTOs\EditProfileDto;
use Modules\User\DTOs\UserDto;
use Modules\User\Models\User;

class UserService
{
    public function updateProfile(User $user, UserDto|EditProfileDto $dto): User
    {
        $user->update($dto->toArray());

        return $user->fresh();
    }
}
