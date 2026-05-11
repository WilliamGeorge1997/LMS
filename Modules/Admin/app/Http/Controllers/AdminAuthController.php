<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Modules\Admin\Http\Requests\AdminLoginRequest;
use Modules\Admin\Models\Admin;

class AdminAuthController extends Controller implements HasMiddleware
{

    public static function middleware(): array
    {
        return [
            new Middleware('guest:admin', except: ['logout'])
        ];
    }

    /**
     * Show login form.
     */

    public function loginForm()
    {
        return view('admin::login');
    }

    public function login(AdminLoginRequest $request)
    {
        $admin = Admin::where('email', $request->email)->first();

        if (!$admin || !Hash::check($request->string('password'), $admin->password)) {
            return back()->withErrors(['email' => __('admin::messages.invalid_credenetials')])->withInput($request->only('email', 'remember_me'));
        }

        if (!$admin->is_active) {
            return back()->withErrors(['email' => __('admin::messages.unactive_account')])->withInput($request->only('email', 'remember_me'));
        }

        Auth::guard('admin')->attempt($request->only(['email', 'password']), $request->boolean('remember_me'));
        $request->session()->regenerate();

        return redirect()->intended(route('admin.dashboard'));
    }

    public function logout()
    {
        Auth::guard('admin')->logout();
        return redirect()->route('admin.login');
    }
}
