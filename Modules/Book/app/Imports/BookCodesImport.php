<?php

namespace Modules\Book\Imports;

use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithProgressBar;
use Maatwebsite\Excel\Concerns\Importable;
use Modules\Book\Models\Book;
use Modules\Book\Models\BookCode;
use Modules\Book\Enums\BookCodeType;

class BookCodesImport implements ToModel, WithHeadingRow, WithBatchInserts, WithChunkReading, WithProgressBar
{
    use Importable;
    /**
     * Map of excel names to database names for dissimilar ones.
     */
    protected array $nameMap = [
        'Maths Blocks 1' => 'Maths Blocks 1 - SB',
        'Maths Blocks 2' => 'Maths Blocks 2 - SB',
        'Dont Give up' => 'Don’t Give Up!',
        'What a Goal' => 'What a Goal!',
        'Railway Children' => 'The Railway Children',
    ];

    /**
     * Cache for book IDs to avoid querying the DB for every row.
     */
    protected array $bookIds = [];

    public int $importedCount = 0;
    public int $skippedCount = 0;
    public string $tenantId;

    public function __construct(string $tenantId)
    {
        $this->tenantId = $tenantId;
    }

    public function model(array $row): Model|array|null
    {
        $bookName = trim((string) ($row['bookname'] ?? ''));

        if (empty($bookName)) {
            $this->skippedCount++;
            return null;
        }

        // Apply mapping for non-matching names
        if (isset($this->nameMap[$bookName])) {
            $bookName = $this->nameMap[$bookName];
        }

        $bookId = $this->getBookId($bookName);

        if (!$bookId) {
            $this->skippedCount++;
            return null; // Skip if book not found
        }

        // Determine type: 1 = Student, otherwise Teacher
        $isStudent = ($row['isstudent'] == 1);
        $type = $isStudent ? BookCodeType::Student->value : BookCodeType::Teacher->value;

        // Skip if code already exists to prevent duplicates
        if (BookCode::where('code', $row['accesscode'])->exists()) {
            $this->skippedCount++;
            return null;
        }

        $this->importedCount++;

        return new BookCode([
            'book_id' => $bookId,
            'code' => $row['accesscode'],
            'type' => $type,
            'is_active' => true,
            'is_used' => false,
            'tenant_id' => $this->tenantId,
            'duration' => 8,
        ]);
    }

    protected function getBookId(string $bookName): ?int
    {
        if (array_key_exists($bookName, $this->bookIds)) {
            return $this->bookIds[$bookName];
        }

        // Search by Spatie Translatable title or basic like query
        $book = Book::where('title->en', $bookName)
            ->orWhere('title->ar', $bookName)
            ->orWhere('title', 'LIKE', '%' . $bookName . '%')
            ->first();

        if ($book) {
            $this->bookIds[$bookName] = $book->id;
            return $book->id;
        }

        $this->bookIds[$bookName] = null;
        return null;
    }

    public function batchSize(): int
    {
        return 1000;
    }

    public function chunkSize(): int
    {
        return 1000;
    }
}
