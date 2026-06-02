<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Book\Enums\BookCodeType;
use Modules\Book\Models\Book;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('book_codes', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Book::class)->index()->constrained()->cascadeOnDelete();
            $table->string('tenant_id');
            $table->string('code')->unique();
            $table->unsignedSmallInteger('duration');
            $table->enum('type', BookCodeType::values());
            $table->date('from')->nullable();
            $table->date('to')->nullable();
            $table->boolean('is_used')->default(false);
            $table->boolean('is_active')->default(false);
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->cascadeOnDelete();
            $table->index(['id', 'tenant_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('book_codes');
    }
};
