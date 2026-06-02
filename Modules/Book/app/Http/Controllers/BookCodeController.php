<?php

namespace Modules\Book\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Modules\Admin\Enums\Role;
use Modules\Book\DTOs\BookCodeDto;
use Modules\Book\Http\Requests\BookCodeStoreRequest;
use Modules\Book\Models\BookCode;
use Modules\Book\Services\BookCodeService;
use Modules\Book\ViewModel\BookViewModel;
use Modules\Common\Helpers\AjaxResponse;

class BookCodeController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            'auth:admin',
            'role:' . Role::SUPER_ADMIN->value . '|' . Role::MANAGER->value,
            'set.locale',
        ];
    }

    public function __construct(private readonly BookCodeService $bookCodeService)
    {
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->bookCodeService->dataTable();
        }

        $viewModel = new BookViewModel;

        return view('book::book-codes.index', compact('viewModel'));
    }

    public function store(BookCodeStoreRequest $request): JsonResponse
    {
        $dto = BookCodeDto::fromRequest($request);
        $codes = $this->bookCodeService->save($dto);

        return AjaxResponse::success(
            __('book::messages.codes_generated_successfully', ['count' => $codes->count()]),
            $codes,
        );
    }

    public function destroy(BookCode $bookCode): JsonResponse
    {
        $status = $this->bookCodeService->delete($bookCode);

        return $status
            ? AjaxResponse::success(__('book::messages.code_deleted_successfully'))
            : AjaxResponse::error();
    }

    public function toggleActivate(BookCode $bookCode): JsonResponse
    {
        $bookCode = $this->bookCodeService->toggleActivate($bookCode);
        $message = $bookCode->is_active
            ? __('book::messages.code_activated_successfully')
            : __('book::messages.code_deactivated_successfully');

        return AjaxResponse::success($message, $bookCode);
    }
}
