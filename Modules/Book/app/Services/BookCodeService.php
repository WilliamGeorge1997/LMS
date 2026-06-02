<?php

namespace Modules\Book\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Modules\Book\DTOs\BookCodeDto;
use Modules\Book\Enums\BookCodeType;
use Modules\Book\Models\Book;
use Modules\Book\Models\BookCode;
use Symfony\Component\HttpFoundation\JsonResponse;
use Yajra\DataTables\Facades\DataTables;

class BookCodeService
{
    public function dataTable(): JsonResponse
    {
        $query = BookCode::query()
            ->select([
                'id',
                'book_id',
                'code',
                'duration',
                'type',
                'is_active',
                'is_used',
                'from',
                'to',
                'created_at',
            ])
            ->with(['book:id,title'])
            ->latest('id');

        return DataTables::eloquent($query)
            ->skipTotalRecords()
            ->addColumn('book_title', function (BookCode $bookCode) {
                return $bookCode->book?->getTranslation('title', app()->getLocale())
                    ?? $bookCode->book?->getTranslation('title', 'en');
            })
            ->addColumn('type_label', function (BookCode $bookCode) {
                return BookCodeType::from($bookCode->type)->label();
            })
            ->filterColumn('code', function ($query, $keyword) {
                $query->where('book_codes.code', 'like', "%{$keyword}%");
            })
            ->filterColumn('book_title', function ($query, $keyword) {
                $query->whereHas('book', function ($bookQuery) use ($keyword) {
                    $bookQuery->whereJsonContainsLocale('title', 'en', "%{$keyword}%", 'like')
                        ->orWhereJsonContainsLocale('title', 'ar', "%{$keyword}%", 'like');
                });
            })
            ->toJson();
    }

    public function save(BookCodeDto $dto): Collection
    {
        $book = Book::query()->byTenant()->findOrFail($dto->book_id);
        $records = collect();

        DB::transaction(function () use ($dto, $book, $records) {
            foreach ($dto->codes() as $code) {
                $records->push(BookCode::create($dto->toArray($code, $book->tenant_id)));
            }
        });

        return $records;
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
