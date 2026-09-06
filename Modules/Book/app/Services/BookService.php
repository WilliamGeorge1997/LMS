<?php

namespace Modules\Book\Services;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Modules\Book\DTOs\BookDto;
use ZipArchive;
use Modules\Book\Models\Book;
use Modules\Book\Models\BookCode;
use Modules\Common\Traits\UploaderTrait;
use Modules\User\Models\User;
use Symfony\Component\HttpFoundation\JsonResponse;
use Yajra\DataTables\Facades\DataTables;

class BookService
{
    use UploaderTrait;

    private string $uploadFolder = 'book/cover';

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
            $data['cover'] = $this->uploadImage($dto->cover, $this->uploadFolder, tenantId: $dto->tenant_id);
        }

        return Book::create($data);
    }

    public function update(Book $book, BookDto $dto): Book
    {
        $data = $dto->toArray();
        if ($dto->cover) {
            if ($book->getRawOriginal('cover')) {
                $this->deleteFile($this->uploadFolder, $book->getRawOriginal('cover'), tenantId: $book->tenant_id);
            }

            $data['cover'] = $this->uploadImage($dto->cover, $this->uploadFolder, tenantId: $book->tenant_id);
        }

        $book->update($data);

        return $book->fresh();
    }

    public function delete(Book $book): bool
    {
        if ($book->getRawOriginal('cover')) {
            $this->deleteFile($this->uploadFolder, $book->getRawOriginal('cover'), tenantId: $book->tenant_id);
        }

        if ($book->getRawOriginal('path')) {
            $this->deleteBookPath($book->getRawOriginal('path'), $book->tenant_id);
        }

        return (bool) $book->delete();
    }

    public function toggleActivate(Book $book): Book
    {
        $book->update(['is_active' => !$book->is_active]);

        return $book->fresh();
    }

    public function processBookUpload(Request $request, Book $book): array
    {
        $uploadResult = $this->handleChunkUpload($request);

        if ($uploadResult === false) {
            return ['error' => 'Upload failed'];
        }

        if (is_int($uploadResult)) {
            return ['progress' => $uploadResult];
        }

        $admin = auth('admin')->user();
        $tenantId = $admin && $admin->hasRole(\Modules\Admin\Enums\Role::SUPER_ADMIN->value) ? session('admin_tenant_id') : ($admin ? $admin->tenant_id : null);
        
        $this->extractAndSaveZip($uploadResult, $book, $tenantId);
        
        return ['path' => $book->path];
    }

    private function extractAndSaveZip(UploadedFile $file, Book $book, ?string $tenantId): void
    {
        if ($book->getRawOriginal('path')) {
            $this->deleteBookPath($book->getRawOriginal('path'), $tenantId);
        }

        $uniqueId = uniqid() . '_' . time();
        $tenantPath = $tenantId ? $tenantId . '/' : 'central/';
        $downloadDir = "uploads/{$tenantPath}book/download";
        
        if (!Storage::disk('public')->exists($downloadDir)) {
            Storage::disk('public')->makeDirectory($downloadDir);
        }

        $finalZipPath = Storage::disk('public')->path("{$downloadDir}/{$uniqueId}.zip");
        File::move($file->getPathname(), $finalZipPath);

        $extractDir = Storage::disk('public')->path("uploads/{$tenantPath}book/{$uniqueId}");
        File::makeDirectory($extractDir, 0777, true);

        $zip = new ZipArchive;
        if ($zip->open($finalZipPath) === TRUE) {
            $zip->extractTo($extractDir);
            $zip->close();
        }

        $book->update(['path' => $uniqueId]);
    }

    private function deleteBookPath(string $path, ?string $tenantId): void
    {
        $tenantPath = $tenantId ? $tenantId . '/' : 'central/';
        $downloadZip = "uploads/{$tenantPath}book/download/{$path}.zip";
        if (Storage::disk('public')->exists($downloadZip)) {
            Storage::disk('public')->delete($downloadZip);
        }

        $extractDir = Storage::disk('public')->path("uploads/{$tenantPath}book/{$path}");
        if (File::exists($extractDir)) {
            File::deleteDirectory($extractDir);
        }
    }

    //For API
    public function findByUser(User $user)
    {
        $bookCodes = BookCode::query()
            ->with(['book' => function ($query) {
                $query->active();
            }])
            ->where('user_id', $user->id)
            ->where('is_used', true)
            ->where('is_active', true)
            ->where('from', '<=', now()->toDateString())
            ->where('to', '>=', now()->toDateString())
            ->get();

        return $bookCodes->whereNotNull('book')->unique('book_id')->pluck('book')->values();
    }
}
