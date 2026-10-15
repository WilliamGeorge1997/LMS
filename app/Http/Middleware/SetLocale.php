<?php

namespace App\Http\Middleware;

use App\Enums\Locale;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $default = Locale::ENGLISH->value;

        $language = $default;

        if (auth('admin')->check()) {
            $language = auth('admin')->user()?->language ?? $default;
        } elseif (session()->has('locale')) {
            $language = session('locale');
        } elseif ($request->cookie('app_locale')) {
            $language = $request->cookie('app_locale');
            session(['locale' => $language]);
        }

        app()->setLocale($language);

        return $next($request);
    }
}
