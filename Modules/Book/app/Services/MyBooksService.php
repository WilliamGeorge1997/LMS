<?php

namespace Modules\Book\Services;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Modules\Book\Models\BookCode;
use Modules\User\Models\User;

class MyBooksService
{
    public function __construct(private readonly BookCodeService $bookCodeService) {}

    /**
     * Get active or assigned books for the given user.
     */
    public function getUserBooks(User $user): Collection
    {
        $books = $user->activeBooks()
            ->with(['publisher'])
            ->get();

        if ($books->isEmpty()) {
            $books = $user->books()
                ->with(['publisher'])
                ->get();
        }

        return $books;
    }

    /**
     * Claim an access code for the given user.
     */
    public function claimCode(string $code, User $user): BookCode
    {
        return DB::transaction(function () use ($code, $user) {
            $cleanedCode = trim($code);
            $userType = $user->type?->value ?? 'student';

            $bookCode = $this->bookCodeService->check($cleanedCode, $userType);

            return $this->bookCodeService->redeem($bookCode, $user);
        });
    }
}
