<?php

namespace Modules\Book\Exports;

use Maatwebsite\Excel\Concerns\Export;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Modules\Book\Models\Book;

use Modules\Book\DTOs\BookCodesExportFilters;
use Modules\Book\Models\BookCode;

class BookCodesExport implements Export, WithMultipleSheets
{
    use Exportable;

    public function __construct(private readonly BookCodesExportFilters $filters)
    {
    }

    public function sheets(): array
    {
        $sheets = [];

        $booksQuery = Book::query()->byTenant()->whereHas('bookCodes', function ($query) {
            $this->applyFilters($query);
        });

        if (!empty($this->filters->book_ids)) {
            $booksQuery->whereIn('id', $this->filters->book_ids);
        }

        $books = $booksQuery->get(['id', 'title']);

        foreach ($books as $book) {
            /**
             * @var Book $book
             */
            $title = $book->getTranslation('title', 'en');

            $sheets[] = new BookCodeSheetExport($book->id, $title, $this->filters);
        }

        return $sheets;
    }

    private function applyFilters($query)
    {
        if ($this->filters->type && $this->filters->type !== 'all') {
            $query->where('type', $this->filters->type);
        }

        if ($this->filters->is_used !== null && $this->filters->is_used !== 'all') {
            $query->where('is_used', $this->filters->is_used === '1');
        }

        if ($this->filters->is_active !== null && $this->filters->is_active !== 'all') {
            $query->where('is_active', $this->filters->is_active === '1');
        }

        if ($this->filters->school_id) {
            $query->where('school_id', $this->filters->school_id);
        }

        if ($this->filters->from_date) {
            $query->whereDate('created_at', '>=', $this->filters->from_date);
        }

        if ($this->filters->to_date) {
            $query->whereDate('created_at', '<=', $this->filters->to_date);
        }
    }
}
