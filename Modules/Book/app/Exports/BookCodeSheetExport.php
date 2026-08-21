<?php

namespace Modules\Book\Exports;

use Illuminate\Database\Query\Builder;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Maatwebsite\Excel\Columns\Text;
use Maatwebsite\Excel\Concerns\Export;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithColumns;
use Maatwebsite\Excel\Concerns\WithCustomChunkSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Modules\Book\Models\BookCode;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class BookCodeSheetExport implements Export, FromQuery, WithTitle, WithColumns, WithCustomChunkSize, WithStyles
{
    public function __construct(
        private readonly int $bookId,
        private readonly string $bookTitle
    ) {}

    public function query(): Builder|EloquentBuilder|Relation
    {
        return BookCode::query()->select(['code'])->where('book_id', $this->bookId)->orderBy('id');
    }

    public function columns(): array
    {
        return [
            Text::make('Access Code', 'code')->width(30),
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => [
                    'bold' => true,
                ],
                'fill' => [
                    'fillType'   => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E0E0E0']
                ],
            ],
        ];
    }

    public function title(): string
    {
        $title = str_replace(['*', ':', '?', '[', ']'], '', $this->bookTitle);
        return mb_substr($title, 0, 31);
    }

    public function chunkSize(): int
    {
        return 5000;
    }
}
