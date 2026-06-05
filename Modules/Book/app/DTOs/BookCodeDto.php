<?php

namespace Modules\Book\DTOs;

use Illuminate\Support\Str;
use Modules\Book\Enums\BookCodeType;
use Modules\Book\Http\Requests\BookCodeStoreRequest;
use Modules\Book\Models\BookCode;

class BookCodeDto
{
    public function __construct(
        public readonly int $book_id,
        public readonly int $duration,
        public readonly BookCodeType $type,
        public readonly int $quantity,
        public readonly string $tenant_id,
    ) {
    }

    public static function fromRequest(BookCodeStoreRequest $request): self
    {
        $quantity = $request->filled('quantity')
            ? (int) $request->input('quantity')
            : 1;

        return new self(
            book_id: (int) $request->input('book_id'),
            duration: (int) $request->input('duration'),
            type: BookCodeType::from($request->input('type')),
            quantity: max(1, $quantity),
            tenant_id: $request->input('tenant_id'),
        );
    }

    /**
     * @return list<string>
     */
    public function codes(string $isbn): array
    {
        $codes = [];

        for ($i = 0; $i < $this->quantity; $i++) {
            $codes[] = $this->generateUniqueCode($isbn);
        }

        return $codes;
    }

    public function toArray(string $code): array
    {
        return [
            'book_id' => $this->book_id,
            'tenant_id' => $this->tenant_id,
            'code' => $code,
            'duration' => $this->duration,
            'type' => $this->type->value,
            'is_active' => 1,
            'is_used' => 0,
        ];
    }

    private function generateUniqueCode(string $isbn): string
    {
        $isbn = str_replace(['-', ' '], '', $isbn);
        $suffix = '-' . $this->type->suffix();

        do {
            $tail = substr($isbn, -4);
            $isbn4 = substr(strtoupper(Str::random(4 - strlen($tail))) . $tail, -4);
            $code = $isbn4 . strtoupper(Str::random(6)) . $suffix;
        } while (BookCode::withoutTenancy()->where('code', $code)->exists());

        return $code;
    }
}
