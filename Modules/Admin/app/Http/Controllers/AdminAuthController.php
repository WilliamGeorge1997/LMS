<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Modules\Admin\Enums\Role;
use Modules\Admin\Http\Requests\AdminLoginRequest;
use Modules\Admin\Models\Admin;
use Modules\Common\Helpers\AjaxResponse;

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
        return view('admin::login');
    }

    public function login(AdminLoginRequest $request)
    {

        $currentHost = $request->getHost();
        $isCentralDomain = in_array($currentHost, config('tenancy.central_domains'), true);

        // Bypass BelongsToTenant global scope
        /** @var Admin $admin */
        $admin = Admin::withoutTenancy()->where('email', $request->string('email'))->first();

        if (! $admin) {
            return AjaxResponse::validationError(['email' => __('admin::messages.invalid_credentials')]);
        }

        if (! Hash::check($request->string('password'), $admin->password)) {
            return AjaxResponse::validationError(['email' => __('admin::messages.invalid_credentials')]);
        }

        if (! $admin->is_active) {
            return AjaxResponse::validationError(['email' => __('admin::messages.unactive_account')]);
        }

        // Case manager and central domin
        if ($isCentralDomain && $admin->hasRole(Role::MANAGER->value)) {
            return AjaxResponse::validationError(['email' => __('admin::messages.manager_central_login')]);
        }

        // Case super admin and tenant subdomain
        if (! $isCentralDomain && $admin->hasRole(Role::SUPER_ADMIN->value)) {
            return AjaxResponse::validationError(['email' => __('admin::messages.super_admin_subdomain_login')]);
        }

        // Case manager and tenant subdomain
        if (! $isCentralDomain && $admin->hasRole(Role::MANAGER->value)) {
            if ($admin->tenant_id !== tenant()->getTenantKey()) {
                return AjaxResponse::validationError(['email' => __('admin::messages.manager_wrong_subdomain')]);
            }
        }

        Auth::guard('admin')->attempt($request->only(['email', 'password']), $request->boolean('remember_me'));
        $request->session()->regenerate();

        return AjaxResponse::success('Logged in successfully.');
    }

    public function logout()
    {
        Auth::guard('admin')->logout();
        // Case super admin include session tenant id
        session()->forget('admin_tenant_id');

        return redirect(url('/admin/login'));
    }
}
