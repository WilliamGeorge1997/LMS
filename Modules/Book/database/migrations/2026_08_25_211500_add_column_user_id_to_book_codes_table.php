<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\User\Models\User;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('book_codes', function (Blueprint $table) {
            $table->foreignIdFor(User::class)->after('tenant_id')->nullable()->constrained()->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('book_codes', function (Blueprint $table) {
            $table->dropConstrainedForeignIdFor(User::class);
        });
    }
};
