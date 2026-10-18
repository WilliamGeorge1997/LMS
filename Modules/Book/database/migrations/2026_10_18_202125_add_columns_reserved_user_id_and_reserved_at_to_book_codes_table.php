<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\User\Models\User;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('book_codes', function (Blueprint $table) {
            $table->foreignIdFor(User::class, 'reserved_user_id')
                ->after('to')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamp('reserved_at')
                ->after('reserved_user_id')
                ->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('book_codes', function (Blueprint $table) {
            $table->dropConstrainedForeignIdFor(User::class, 'reserved_user_id');
            $table->dropColumn('reserved_at');
        });
    }
};
