<?php

namespace Modules\Book\Exports;

use Maatwebsite\Excel\Concerns\Export;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Modules\Book\Models\Book;

class BookCodesExport implements Export, WithMultipleSheets
{
    use Exportable;

    public function sheets(): array
    {
        $sheets = [];

        $books = Book::whereHas('bookCodes')->get(['id', 'title']);

        foreach ($books as $book) {
            /**
             * @var Book $book
             */
            $title = $book->getTranslation('title', 'en');

            $sheets[] = new BookCodeSheetExport($book->id, $title);
        }

        return $sheets;
    }
}
