<?php

namespace Modules\Book\Services;

use Modules\Book\DTOs\BookDto;
use Modules\Book\Models\Book;
use Symfony\Component\HttpFoundation\JsonResponse;
use Yajra\DataTables\Facades\DataTables;

class BookService
{
    public function findAll(array $data)
    {
        $query = Book::query()->latest('id');

        return getCaseCollection($query, $data);
    }

    public function dataTable(): JsonResponse
    {
        $query = Book::query()
            ->select([
                'id',
                'title',
                'isbn',
                'publisher_id',
                'category_id',
                'level_id',
                'tenant_id',
                'is_active',
                'created_at',
            ])
            ->with([
                'publisher:id,name',
                'category:id,title',
                'level:id,title',
                'tenant:id,name',
            ]);

        return DataTables::eloquent($query)
            ->addColumn('title_en', function (Book $book) {
                return $book->getTranslation('title', 'en');
            })
            ->addColumn('title_ar', function (Book $book) {
                return $book->getTranslation('title', 'ar');
            })
            ->toJson();
    }

    public function findBy(string $key, string $value, array $columns = ['*'])
    {
        return Book::query()->active()->where($key, $value)->get($columns);
    }

    public function active()
    {
        return Book::query()->active()->orderBy('title')->get(['id', 'title']);
    }

    public function findByTenant(array $columns = ['*'])
    {
        return Book::query()->active()->byTenant()->orderBy('title')->get($columns);
    }

    public function save(BookDto $dto): Book
    {
        return Book::create($dto->toArray());
    }

    public function update(Book $book, BookDto $dto): Book
    {
        $book->update($dto->toArray());

        return $book->fresh();
    }

    public function delete(Book $book): bool
    {
        return (bool) $book->delete();
    }

    public function toggleActivate(Book $book): Book
    {
        $book->update(['is_active' => !$book->is_active]);

        return $book->fresh();
    }
}
