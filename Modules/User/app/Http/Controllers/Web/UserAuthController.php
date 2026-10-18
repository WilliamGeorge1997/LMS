<?php

namespace Modules\User\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use Modules\Book\Services\BookCodeService;
use Modules\Common\Helpers\AjaxResponse;
use Modules\Country\Services\CountryService;
use Modules\User\DTOs\UserDto;
use Modules\User\Emails\ForgetPasswordMail;
use Modules\User\Emails\RegisterMail;
use Modules\User\Http\Requests\Web\UserLoginRequest;
use Modules\User\Http\Requests\Web\UserRegisterRequest;
use Modules\User\Http\Requests\Web\UserVerifyRequest;
use Modules\User\Models\User;
use Override;

class UserAuthController extends Controller implements HasMiddleware
{
    #[Override]
    public static function middleware(): array
    {
        return [
            new Middleware('guest:user_web', except: ['logout']),
            new Middleware('auth:user_web', only: ['logout']),
        ];
    }

    public function showSelectType()
    {
        return view('user::auth.select-type');
    }

    public function selectType(Request $request, ?string $type = null)
    {
        $selectedType = $type ?? $request->input('type');

        if (in_array($selectedType, ['student', 'teacher'], true)) {
            session(['user_type' => $selectedType]);
        }

        return redirect()->route('login');
    }

    public function showLogin(Request $request)
    {
        if ($request->has('type')) {
            $type = $request->query('type');
            if (in_array($type, ['student', 'teacher'], true)) {
                session(['user_type' => $type]);
            }

            return redirect()->route('login');
        }

        $type = session('user_type', 'student');

        return view('user::auth.login', compact('type'));
    }

    public function login(UserLoginRequest $request)
    {
        $credentials = $request->validated();

        $field = filter_var($credentials['login'], FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $user = User::where($field, $credentials['login'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            return AjaxResponse::validationError([
                'login' => [__('user::message.credentials')],
            ]);
        }

        if (! $user->is_active) {
            $newCode = (string) rand(100000, 999999);
            $user->update(['verify_code' => $newCode]);

            Mail::to($user->email)->send((new RegisterMail($newCode))->onConnection('database'));

            session([
                'verify_email' => $user->email,
                'verify_type' => $user->type,
            ]);

            return AjaxResponse::success(__('user::message.code_resent'), [
                'redirect' => route('verify.show'),
            ]);
        }

        Auth::guard('user_web')->login($user, $request->boolean('remember'));

        $request->session()->regenerate();

        return AjaxResponse::success(__('user::message.welcome_back', ['type' => $user->type]), [
            'redirect' => session()->pull('url.intended', url('/my-books')),
        ]);
    }

    public function showRegister(Request $request, CountryService $countryService)
    {
        if ($request->has('type')) {
            $type = $request->query('type');
            if (in_array($type, ['student', 'teacher'], true)) {
                session(['user_type' => $type]);
            }

            return redirect()->route('register');
        }

        $type = session('user_type', 'student');
        $countries = $countryService->active();

        return view('user::auth.register', compact('countries', 'type'));
    }

    public function register(UserRegisterRequest $request, BookCodeService $bookCodeService)
    {
        $dto = UserDto::fromWebRegisterRequest($request);

        try {
            $user = DB::transaction(function () use ($dto, $bookCodeService) {
                $bookCode = $bookCodeService->check($dto->code, $dto->type);

                $user = User::create($dto->toArray());

                $bookCodeService->reserve($bookCode, $user);

                return $user;
            });

            if ($user->verify_code) {
                Mail::to($user->email)->send((new RegisterMail($user->verify_code))->onConnection('database'));
            }

            session([
                'verify_email' => $user->email,
                'verify_type' => $user->type,
            ]);

            return redirect()->route('verify.show')->with('success', __('user::message.registered'));
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }
    }

    public function showVerify()
    {
        $email = session('verify_email');

        if (! $email) {
            return redirect()->route('welcome');
        }

        $type = session('verify_type', 'student');

        return view('user::auth.verify', compact('email', 'type'));
    }

    public function verify(UserVerifyRequest $request, BookCodeService $bookCodeService)
    {
        $validated = $request->validated();
        $email = session('verify_email');
        $otp = $validated['otp'];

        if (! $email) {
            return AjaxResponse::validationError([
                'otp' => [__('user::message.credentials')],
            ]);
        }

        $user = User::where('email', $email)->first();

        if (! $user) {
            return AjaxResponse::validationError([
                'otp' => [__('user::message.credentials')],
            ]);
        }

        if ($user->is_active) {
            Auth::guard('user_web')->login($user);
            $request->session()->regenerate();
            session()->forget(['verify_email', 'verify_type']);

            return AjaxResponse::success(__('user::message.already_verified'), [
                'redirect' => url('/my-books'),
            ]);
        }

        if ((string) $user->verify_code === $otp) {
            $user->update([
                'is_active' => true,
                'verify_code' => null,
            ]);

            $bookCodeService->redeemReserved($user);

            Auth::guard('user_web')->login($user);
            $request->session()->regenerate();
            session()->forget(['verify_email', 'verify_type']);

            return AjaxResponse::success(__('user::message.account_activated'), [
                'redirect' => url('/my-books'),
            ]);
        }

        return AjaxResponse::validationError([
            'otp' => [__('user::message.wrong_otp')],
        ]);
    }

    public function resendVerifyCode(Request $request)
    {
        $email = session('verify_email');

        if (! $email) {
            return AjaxResponse::validationError([
                'email' => [__('user::message.credentials')],
            ]);
        }

        $user = User::where('email', $email)->first();

        if ($user) {
            if ($user->is_active) {
                return AjaxResponse::error(__('user::message.already_verified'), 'bad_request');
            }

            $newCode = (string) rand(100000, 999999);
            $user->update(['verify_code' => $newCode]);

            Mail::to($user->email)->send((new RegisterMail($newCode))->onConnection('database'));

            return AjaxResponse::success(__('user::message.code_resent'));
        }

        return AjaxResponse::error(__('user::message.credentials'), 'not_found');
    }

    // Forgot Password Flow
    public function showForgotPassword()
    {
        return view('user::auth.forgot-password');
    }

    public function sendResetCode(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ]);

        $email = trim(strtolower($request->input('email')));
        $user = User::where('email', $email)->first();

        $verifyCode = (string) rand(100000, 999999);
        $user->update(['verify_code' => $verifyCode]);

        Mail::to($email)->send((new ForgetPasswordMail($verifyCode))->onConnection('database'));

        session(['reset_password_email' => $email]);

        return AjaxResponse::success(__('user::message.reset_sent'), [
            'redirect' => route('password.verify.show'),
        ]);
    }

    public function showVerifyReset()
    {
        $email = session('reset_password_email');

        if (! $email) {
            return redirect()->route('password.request');
        }

        return view('user::auth.verify-reset', compact('email'));
    }

    public function verifyResetCode(Request $request)
    {
        $email = session('reset_password_email');

        if (! $email) {
            return AjaxResponse::validationError([
                'otp' => [__('user::message.credentials')],
            ]);
        }

        $request->validate([
            'otp' => ['required', 'string', 'size:6'],
        ]);

        $otp = trim($request->input('otp', ''));
        $user = User::where('email', $email)->first();

        if ($user && (string) $user->verify_code === $otp) {
            session([
                'password_reset_verified' => true,
                'reset_password_email' => $email,
            ]);

            return AjaxResponse::success(__('user::message.valid_otp'), [
                'redirect' => route('password.reset'),
            ]);
        }

        return AjaxResponse::validationError([
            'otp' => [__('user::message.wrong_otp')],
        ]);
    }

    public function resendResetCode(Request $request)
    {
        $email = session('reset_password_email');

        if (! $email) {
            return AjaxResponse::validationError([
                'email' => [__('user::message.credentials')],
            ]);
        }

        $user = User::where('email', $email)->first();

        if ($user) {
            $newCode = (string) rand(100000, 999999);
            $user->update(['verify_code' => $newCode]);

            Mail::to($user->email)->send((new ForgetPasswordMail($newCode))->onConnection('database'));

            return AjaxResponse::success(__('user::message.code_resent'));
        }

        return AjaxResponse::error(__('user::message.credentials'), 'not_found');
    }

    public function showResetPassword()
    {
        $email = session('reset_password_email');

        if (! session('password_reset_verified') || ! $email) {
            return redirect()->route('password.request');
        }

        return view('user::auth.reset-password', compact('email'));
    }

    public function updatePassword(Request $request)
    {
        $email = session('reset_password_email');

        if (! $email) {
            return AjaxResponse::error(__('user::message.credentials'), 'not_found');
        }

        $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::where('email', $email)->first();

        if ($user) {
            $user->update([
                'password' => Hash::make($request->input('password')),
                'verify_code' => null,
            ]);

            session()->forget(['password_reset_verified', 'reset_password_email']);

            return AjaxResponse::success(__('user::message.password_changed'), [
                'redirect' => route('login'),
            ]);
        }

        return AjaxResponse::error(__('user::message.credentials'), 'not_found');
    }

    public function logout(Request $request)
    {
        Auth::guard('user_web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
