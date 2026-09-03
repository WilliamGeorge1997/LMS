<?php

namespace Modules\Book\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Modules\Book\Http\Requests\Api\RedeemCodeRequest;
use Modules\Book\Services\BookCodeService;
use Override;

class BookCodeController extends Controller implements HasMiddleware
{
    #[Override]
    public static function middleware(): array
    {
        return [
            'auth:user',
        ];
    }

    public function __construct(private readonly BookCodeService $bookCodeService)
    {
    }

    public function redeem(RedeemCodeRequest $request): JsonResponse
    {
        $user = $request->user();
        $code = $request->validated('code');

        try {
            $bookCode = DB::transaction(function () use ($user, $code) {
                $bookCode = $this->bookCodeService->check($code, $user->type->value);

                return $this->bookCodeService->redeem($bookCode, $user);
            });

            return apiResponse(
                true,
                __('book::messages.code_redeemed_successfully'),
                $bookCode->load('book')
            );
        } catch (ValidationException $e) {
            return apiResponse(false, 'Validation errors', $e->errors(), 'validation_error');
        }
    }
}
