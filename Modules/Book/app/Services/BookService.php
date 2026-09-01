<?php

namespace Modules\Book\Services;

use Modules\Book\DTOs\BookDto;
use Modules\Book\Models\Book;
use Modules\Common\Traits\UploaderTrait;
use Symfony\Component\HttpFoundation\JsonResponse;
use Yajra\DataTables\Facades\DataTables;

class BookService
{
    use UploaderTrait;

    private string $uploadFolder = 'cover';

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
                'cover',
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

    public function findFirstByTenant(string $key, string $value, array $columns = ['*']): Book
    {
        return Book::byTenant()->where($key, $value)->firstOrFail($columns);
    }

    public function save(BookDto $dto): Book
    {
        $data = $dto->toArray();
        if ($dto->cover) {
            $data['cover'] = $this->uploadImage($dto->cover, $this->uploadFolder);
        }

        return Book::create($data);
    }

    public function update(Book $book, BookDto $dto): Book
    {
        $data = $dto->toArray();
        if ($dto->cover) {
            if ($book->getRawOriginal('cover')) {
                $this->deleteFile($this->uploadFolder, $book->getRawOriginal('cover'));
            }

            $data['cover'] = $this->uploadImage($dto->cover, $this->uploadFolder);
        }

        $book->update($data);

        return $book->fresh();
    }

    public function delete(Book $book): bool
    {
        if ($book->getRawOriginal('cover')) {
            $this->deleteFile($this->uploadFolder, $book->getRawOriginal('cover'));
        }

        return (bool) $book->delete();
    }

    public function toggleActivate(Book $book): Book
    {
        $book->update(['is_active' => !$book->is_active]);

        return $book->fresh();
    }
}
