<?php

namespace Modules\Book\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Modules\Book\Services\BookService;
use Override;

class BookController extends Controller implements HasMiddleware
{
    #[Override]
    public static function middleware(): array
    {
        return [
            'auth:user_web',
        ];
    }

    public function __construct(private readonly BookService $bookService) {}

    public function myBooks(Request $request): View
    {
        $user = $request->user();
        $books = $this->bookService->findByUser($user);

        return view('book::web.books', compact('user', 'books'));
    }
}
