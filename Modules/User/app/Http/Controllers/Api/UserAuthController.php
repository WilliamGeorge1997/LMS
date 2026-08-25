<?php

namespace Modules\User\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Modules\Book\Services\BookCodeService;
use Modules\User\DTOs\UserDto;
use Modules\User\Http\Requests\UserLoginRequest;
use Modules\User\Http\Requests\UserRegisterRequest;
use Modules\User\Models\User;

class UserAuthController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('auth:sanctum', only: ['logout']),
        ];
    }

    public function register(UserRegisterRequest $request, BookCodeService $bookCodeService): JsonResponse
    {
        $dto = UserDto::fromRequest($request);

        try {
            $user = DB::transaction(function () use ($dto, $bookCodeService) {
                $bookCode = $bookCodeService->check($dto->code, $dto->type);

                $user = User::create($dto->toArray());

                $bookCodeService->redeem($bookCode, $user);

                return $user;
            });
        } catch (ValidationException $e) {
            return apiResponse(false, 'Validation errors', $e->errors(), 'validation_error');
        }

        return apiResponse(true, __('user::message.registered'), $user, 'created');
    }


    public function login(UserLoginRequest $request): JsonResponse
    {
        $login = $request->validated('login');
        $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        /**@var User $user */
        $user = User::where($field, $login)->first();

        if (! $user || ! Hash::check($request->validated('password'), $user->password)) {
            return apiResponse(
                false,
                __('user::message.credentials'),
                ['login' => [__('user::message.credentials')]],
                'unauthorized'
            );
        }

        if (! $user->is_active) {
            return apiResponse(false, __('user::message.inactive'), null, 'forbidden');
        }

        $user->tokens()->delete();
        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->respondWithToken($token, $user);
    }


    public function logout(): JsonResponse
    {
        auth('user')->user()->currentAccessToken()->delete();

        return apiResponse(true, __('user::message.logout'));
    }


    // Helper
    protected function respondWithToken(string $token, User $user): JsonResponse
    {
        return apiResponse(true, 'Authenticated User', [
            'access_token' => $token,
            'token_type'   => 'bearer',
            'user'         => $user,
        ]);
    }
}
