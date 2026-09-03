<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Tenant\Models\Tenant;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('countries', function (Blueprint $table) {
            $table->foreignIdFor(Tenant::class)->after('title')->index()->constrained()->cascadeOnDelete();
        });

        Schema::table('cities', function (Blueprint $table) {
            $table->foreignIdFor(Tenant::class)->after('title')->index()->constrained()->cascadeOnDelete();
        });

        Schema::table('regions', function (Blueprint $table) {
            $table->foreignIdFor(Tenant::class)->after('title')->index()->constrained()->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('regions', function (Blueprint $table) {
            $table->dropConstrainedForeignIdFor(Tenant::class);
        });

        Schema::table('cities', function (Blueprint $table) {
            $table->dropConstrainedForeignIdFor(Tenant::class);
        });

        Schema::table('countries', function (Blueprint $table) {
            $table->dropConstrainedForeignIdFor(Tenant::class);
        });
    }
};
