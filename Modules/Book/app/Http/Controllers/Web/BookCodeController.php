<?php

namespace Modules\Book\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Support\Facades\DB;
use Modules\Book\Http\Requests\Web\RedeemCodeRequest;
use Modules\Book\Services\BookCodeService;
use Modules\Common\Helpers\AjaxResponse;
use Override;

class BookCodeController extends Controller implements HasMiddleware
{
    #[Override]
    public static function middleware(): array
    {
        return [
            'auth:user_web',
        ];
    }

    public function __construct(private readonly BookCodeService $bookCodeService) {}

    public function redeem(RedeemCodeRequest $request): JsonResponse
    {
        $user = $request->user();
        $code = $request->validated('code');

        $bookCode = DB::transaction(function () use ($user, $code) {
            $bookCode = $this->bookCodeService->check($code, $user->type->value);

            return $this->bookCodeService->redeem($bookCode, $user);
        });

        $message = __('book::messages.code_redeemed_successfully');
        session()->flash('success', $message);

        return AjaxResponse::success($message, [
            'redirect' => route('my-books'),
            'book' => $bookCode->load('book'),
        ]);
    }
}
