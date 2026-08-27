<?php

namespace Modules\Book\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Modules\Book\DTOs\BookCodeDto;
use Modules\Book\Models\BookCode;
use Modules\User\Models\User;
use Symfony\Component\HttpFoundation\JsonResponse;
use Yajra\DataTables\Facades\DataTables;

class BookCodeService
{
    public function __construct(private readonly BookService $bookService)
    {
    }
    public function dataTable(): JsonResponse
    {
        $query = BookCode::query()
            ->select([
                'id',
                'book_id',
                'tenant_id',
                'code',
                'duration',
                'type',
                'is_active',
                'is_used',
                'from',
                'to',
                'created_at',
            ])
            ->with(['book:id,title', 'tenant:id,name'])
            ->latest('id');

        return DataTables::eloquent($query)
            ->skipTotalRecords()
            ->filterColumn('code', function ($query, $keyword) {
                $query->where('code', 'like', "%{$keyword}%");
            })
            ->filterColumn('book.title', function ($query, $keyword) {
                $query->whereHas('book', function ($bookQuery) use ($keyword) {
                    $bookQuery->whereJsonContainsLocale('title', 'en', "%{$keyword}%", 'like')
                        ->orWhereJsonContainsLocale('title', 'ar', "%{$keyword}%", 'like');
                });
            })
            ->toJson();
    }

    public function save(BookCodeDto $dto): void
    {
        $book = $this->bookService->findFirstByTenant('id', $dto->book_id);

        $codes = $dto->codes($book->isbn);
        $now = now();
        $rows = [];

        foreach ($codes as $code) {
            $rows[] = array_merge($dto->toArray($code), [
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        DB::transaction(function () use ($rows) {
            foreach (array_chunk($rows, 1000) as $chunk) {
                BookCode::insert($chunk);
            }
        });
    }

    public function delete(BookCode $bookCode): bool
    {
        return (bool) $bookCode->delete();
    }

    public function toggleActivate(BookCode $bookCode): BookCode
    {
        $bookCode->update(['is_active' => $bookCode->is_active ? 0 : 1]);

        return $bookCode->fresh();
    }

    public function findBy(string $column, mixed $value, bool $lock = false): ?BookCode
    {
        $query = BookCode::where($column, $value);

        if ($lock) {
            $query->lockForUpdate();
        }

        return $query->first();
    }

    public function check(string $code, string $userType): BookCode
    {
        $bookCode = $this->findBy('code', $code, lock: true);

        if (! $bookCode) {
            throw ValidationException::withMessages([
                'code' => [__('user::message.invalid_code')],
            ]);
        }

        if ($bookCode->is_used) {
            throw ValidationException::withMessages([
                'code' => [__('user::message.code_used')],
            ]);
        }

        if (! $bookCode->is_active) {
            throw ValidationException::withMessages([
                'code' => [__('user::message.code_inactive')],
            ]);
        }

        if ($bookCode->type?->value && $bookCode->type->value !== $userType) {
            throw ValidationException::withMessages([
                'code' => [__('user::message.type_mismatch')],
            ]);
        }

        return $bookCode;
    }

    public function redeem(BookCode $bookCode, User $user): BookCode
    {
        $now = now();

        $bookCode->update([
            'user_id'   => $user->id,
            'is_used'   => true,
            'from'      => $now->toDateString(),
            'to'        => $now->copy()->addMonths((int) $bookCode->duration)->toDateString(),
        ]);

        return $bookCode->fresh();
    }
}
