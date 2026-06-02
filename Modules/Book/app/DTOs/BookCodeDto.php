<?php

namespace Modules\Book\DTOs;

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
    ) {}

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
        );
    }

    /**
     * @return list<string>
     */
    public function codes(): array
    {
        $suffix = $this->type->suffix();
        $codes = [];

        for ($i = 0; $i < $this->quantity; $i++) {
            $codes[] = $this->makeUniqueCode($suffix);
        }

        return $codes;
    }

    public function toArray(string $code, string $tenant_id): array
    {
        return [
            'book_id' => $this->book_id,
            'tenant_id' => $tenant_id,
            'code' => $code,
            'duration' => $this->duration,
            'type' => $this->type->value,
            'is_active' => 1,
            'is_used' => 0,
        ];
    }

    private function makeUniqueCode(string $suffix): string
    {
        do {
            $code = strtoupper(bin2hex(random_bytes(4))) . '-' . $suffix;
        } while (BookCode::withoutTenancy()->where('code', $code)->exists());

        return $code;
    }
}
