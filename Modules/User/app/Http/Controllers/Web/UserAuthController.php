<?php

namespace Modules\User\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Modules\Book\Services\BookCodeService;
use Modules\Country\Services\CountryService;
use Modules\User\DTOs\UserDto;
use Modules\User\Http\Requests\Web\UserLoginRequest;
use Modules\User\Http\Requests\Web\UserRegisterRequest;
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

    public function showLogin()
    {
        return view('user::auth.login');
    }

    public function login(UserLoginRequest $request)
    {
        $credentials = $request->validated();

        $field = filter_var($credentials['login'], FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $user = User::where($field, $credentials['login'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            return back()->withErrors([
                'login' => __('user::message.credentials'),
            ])->onlyInput('login');
        }

        if (! $user->is_active) {
            return back()->withErrors([
                'login' => __('user::message.inactive'),
            ]);
        }

        Auth::guard('user_web')->login($user, $request->boolean('remember'));

        $request->session()->regenerate();

        return redirect()->intended('/my-books');
    }

    public function showRegister(CountryService $countryService)
    {
        $countries = $countryService->active();
        return view('user::auth.register', compact('countries'));
    }

    public function register(UserRegisterRequest $request, BookCodeService $bookCodeService)
    {
        $dto = UserDto::fromWebRegisterRequest($request);

        try {
            $user = DB::transaction(function () use ($dto, $bookCodeService) {
                $bookCode = $bookCodeService->check($dto->code, $dto->type);

                $user = User::create($dto->toArray());

                $bookCodeService->redeem($bookCode, $user);

                return $user;
            });

            Auth::guard('user_web')->login($user);
            $request->session()->regenerate();

            return redirect('/my-books')->with('success', __('user::message.registered'));
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }
    }

    public function logout(Request $request)
    {
        Auth::guard('user_web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
