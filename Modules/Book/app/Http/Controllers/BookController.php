<?php

namespace Modules\Book\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Modules\Admin\Enums\Role;
use Modules\Book\DTOs\BookDto;
use Modules\Book\Http\Requests\BookStoreRequest;
use Modules\Book\Http\Requests\BookUpdateRequest;
use Modules\Book\Models\Book;
use Modules\Book\Services\BookService;
use Modules\Book\ViewModel\BookViewModel;
use Modules\Common\Helpers\AjaxResponse;

class BookController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            'auth:admin',
            'role:' . Role::SUPER_ADMIN->value . '|' . Role::MANAGER->value,
            'set.locale',
        ];
    }

    public function __construct(private readonly BookService $bookService)
    {
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->bookService->dataTable();
        }

        $viewModel = new BookViewModel;

        return view('book::books.index', compact('viewModel'));
    }

    public function store(BookStoreRequest $request): JsonResponse
    {
        $dto = BookDto::fromRequest($request);
        $book = $this->bookService->save($dto);

        return AjaxResponse::success(__('book::messages.created_successfully'), $book);
    }

    public function edit(Book $book): string
    {
        $viewModel = new BookViewModel;

        return view('book::books.partials.edit', [
            'book' => $book->load(['publisher', 'category', 'level', 'tenant']),
            'viewModel' => $viewModel,
        ])->render();
    }

    public function update(BookUpdateRequest $request, Book $book): JsonResponse
    {
        $dto = BookDto::fromRequest($request);
        $book = $this->bookService->update($book, $dto);

        return AjaxResponse::success(__('book::messages.updated_successfully'), $book);
    }

    public function destroy(Book $book): JsonResponse
    {
        $status = $this->bookService->delete($book);

        return $status
            ? AjaxResponse::success(__('book::messages.deleted_successfully'))
            : AjaxResponse::error();
    }

    public function toggleActivate(Book $book): JsonResponse
    {
        $book = $this->bookService->toggleActivate($book);
        $message = $book->is_active
            ? __('book::messages.activated_successfully')
            : __('book::messages.deactivated_successfully');

        return AjaxResponse::success($message, $book);
    }
}
