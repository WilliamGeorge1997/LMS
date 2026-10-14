<?php

namespace Modules\Book\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Modules\Book\Http\Requests\Web\ClaimBookCodeRequest;
use Modules\Book\Services\MyBooksService;
use Modules\Common\Helpers\AjaxResponse;

class MyBooksController extends Controller
{
    public function __construct(private readonly MyBooksService $myBooksService) {}

    public function index(): View
    {
        $user = auth('user_web')->user();
        $books = $this->myBooksService->getUserBooks($user);

        return view('book::web.my-books', compact('user', 'books'));
    }

    public function claimCode(ClaimBookCodeRequest $request): JsonResponse
    {
        $user = auth('user_web')->user();
        $this->myBooksService->claimCode($request->validated('code'), $user);

        return AjaxResponse::success(__('book::messages.code_redeemed_successfully'), [
            'redirect' => route('my-books'),
        ]);
    }
}
