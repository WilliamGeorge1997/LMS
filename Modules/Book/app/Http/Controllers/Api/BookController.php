<?php

namespace Modules\Book\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
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
            'auth:sanctum',
        ];
    }

    public function __construct(private readonly BookService $bookService) {}


    public function myBooks(Request $request): JsonResponse
    {
        $books = $this->bookService->findBy($request->user());

        return apiResponse(true, 'Books retrieved successfully', $books);
    }
}
