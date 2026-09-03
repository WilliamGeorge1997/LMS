<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\School\Models\School;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('book_codes', function (Blueprint $table) {
            $table->foreignIdFor(School::class)->after('user_id')->nullable()->index()->constrained()->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('book_codes', function (Blueprint $table) {
            $table->dropConstrainedForeignIdFor(School::class);
        });
    }
};
