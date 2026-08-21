<?php

namespace Modules\Book\Services;

use Illuminate\Support\Facades\DB;
use Modules\Book\DTOs\BookCodeDto;
use Modules\Book\Models\BookCode;
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
}
