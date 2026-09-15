<?php

namespace Modules\Book\DTOs;

use Illuminate\Http\Request;

class BookCodesExportFilters
{
    public function __construct(
        public readonly ?string $type = null,
        public readonly ?string $is_used = null,
        public readonly ?string $is_active = null,
        public readonly ?array $book_ids = null,
        public readonly ?string $from_date = null,
        public readonly ?string $to_date = null,
        public readonly ?int $school_id = null
    ) {}

    public static function fromRequest(Request $request): self
    {
        $bookIds = $request->input('book_ids');
        if (is_array($bookIds)) {
            $bookIds = array_filter($bookIds);
            $bookIds = empty($bookIds) ? null : $bookIds;
        } else {
            $bookIds = null;
        }

        return new self(
            type: $request->input('type'),
            is_used: $request->input('is_used'),
            is_active: $request->input('is_active'),
            book_ids: $bookIds,
            from_date: $request->input('from_date'),
            to_date: $request->input('to_date'),
            school_id: $request->input('school_id') ? (int) $request->input('school_id') : null
        );
    }
}
