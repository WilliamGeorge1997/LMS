<?php

namespace Modules\User\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Attributes\Controllers\Middleware;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\PersonalAccessToken;
use Modules\Book\Services\BookCodeService;
use Modules\User\DTOs\UserDto;
use Modules\User\Emails\ForgetPasswordMail;
use Modules\User\Http\Requests\ForgetPasswordRequest;
use Modules\User\Http\Requests\NewPasswordRequest;
use Modules\User\Http\Requests\UserLoginRequest;
use Modules\User\Http\Requests\UserRegisterRequest;
use Modules\User\Http\Requests\VerifyForgetPasswordRequest;
use Modules\User\Models\User;


#[Middleware('auth:user', only: ['logout'] ) ]
class UserAuthController extends Controller
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
            return apiResponse(true, __('user::message.registered'), $user, 'created');
        } catch (ValidationException $e) {
            return apiResponse(false, 'Validation errors', $e->errors(), 'validation_error');
        }
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
        $token = $user->createToken('user_token')->plainTextToken;

        return $this->respondWithToken($token, $user);
    }


    public function logout(): JsonResponse
    {
        /**@var User $user */
        $user = auth('user')->user();

        /**@var PersonalAccessToken $token */
        $token = $user->currentAccessToken();
        $token->delete();


        return apiResponse(true, __('user::message.logout'));
    }

    public function forgetPassword(ForgetPasswordRequest $request): JsonResponse
    {
        $email = $request->validated('email');
        /**@var User $user */
        $user = User::where('email', $email)->first();

        $verifyCode = rand(100000, 999999);
        $user->update(['verify_code' => $verifyCode]);

        // Send email via queue
        Mail::to($email)->send((new ForgetPasswordMail($verifyCode))->onConnection('database'));

        return apiResponse(true, 'Message Sent, please check your email');
    }

    public function verifyForgetPassword(VerifyForgetPasswordRequest $request): JsonResponse
    {
        $data = $request->validated();
        /**@var User $user */
        $user = User::where('email', $data['email'])->first();

        if ($user && $user->verify_code == $data['otp']) {
            return apiResponse(true, 'Valid OTP');
        }

        return apiResponse(false, 'Wrong OTP', null, 'unauthorized');
    }

    public function newPassword(NewPasswordRequest $request): JsonResponse
    {
        $data = $request->validated();
        /**@var User $user */
        $user = User::where('email', $data['email'])->first();

        $user->update([
            'password' => Hash::make($data['password']),
        ]);

        return apiResponse(true, 'Password Changed Successfully');
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
