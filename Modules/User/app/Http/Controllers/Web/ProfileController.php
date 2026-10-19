<?php

namespace Modules\User\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Modules\Common\Helpers\AjaxResponse;
use Modules\Country\Services\CountryService;
use Modules\User\DTOs\EditProfileDto;
use Modules\User\Http\Requests\Web\EditProfileRequest;
use Modules\User\Models\User;
use Modules\User\Services\UserService;
use Override;

class ProfileController extends Controller implements HasMiddleware
{
    #[Override]
    public static function middleware(): array
    {
        return [
            new Middleware('auth:user_web', only: ['show', 'update']),
        ];
    }

    public function __construct(private readonly UserService $userService) {}

    public function show(Request $request, CountryService $countryService): View
    {
        /** @var User $user */
        $user = $request->user()->load(['school', 'country', 'city', 'region']);
        $countries = $countryService->active();

        return view('user::profile', compact('user', 'countries'));
    }

    public function update(EditProfileRequest $request): JsonResponse
    {
        $dto = EditProfileDto::fromRequest($request);

        /** @var User $user */
        $user = $this->userService->updateProfile($request->user(), $dto);

        return AjaxResponse::success(__('user::message.profile_updated'), [
            'redirect' => route('profile'),
            'user' => $user->load(['school', 'country', 'city', 'region']),
        ]);
    }

    public function switchLanguage(Request $request): JsonResponse
    {
        $locale = $request->input('locale');

        if (in_array($locale, ['en', 'ar'], true)) {
            session(['locale' => $locale]);
            cookie()->queue(cookie()->forever('app_locale', $locale));
            app()->setLocale($locale);
        }

        return AjaxResponse::success(__('user::message.profile_updated'), [
            'redirect' => route('profile'),
            'locale' => $locale,
        ])->withCookie(cookie()->forever('app_locale', $locale));
    }

    public function switchTheme(Request $request): JsonResponse
    {
        $theme = $request->input('theme');

        if (in_array($theme, ['light', 'dark'], true)) {
            session(['theme' => $theme]);
            cookie()->queue(cookie()->forever('app_theme', $theme));
        }

        return AjaxResponse::success(__('user::message.profile_updated'), [
            'theme' => session('theme', 'light'),
        ])->withCookie(cookie()->forever('app_theme', $theme));
    }
}
