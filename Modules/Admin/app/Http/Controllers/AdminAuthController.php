<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Modules\Admin\Http\Requests\AdminLoginRequest;
use Modules\Admin\Models\Admin;
use Modules\Common\Helpers\ApiResponse;

class AdminAuthController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('guest:admin', except: ['logout']),
        ];
    }

    /**
     * Show login form.
     */
    public function loginForm()
    {
        return view('admin::lotgin');
    }

    public function login(AdminLoginRequest $request)
    {
        $admin = Admin::where('email', $request->string('email'))->first();

        if (! $admin || ! Hash::check($request->string('password'), $admin->password)) {
            return ApiResponse::validationError(['email' => __('admin::messages.invalid_credentials')]);
        }

        if (! $admin->is_active) {
            return ApiResponse::validationError(['email' => __('admin::messages.unactive_account')]);
        }

        Auth::guard('admin')->attempt($request->only(['email', 'password']), $request->boolean('remember_me'));
        $request->session()->regenerate();

        return ApiResponse::success('Logged in successfully.');
    }

    public function logout()
    {
        Auth::guard('admin')->logout();

        return redirect()->route('admin.login');
    }
}
