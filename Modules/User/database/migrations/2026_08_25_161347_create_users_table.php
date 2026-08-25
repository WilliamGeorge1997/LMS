<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Country\Models\City;
use Modules\Country\Models\Country;
use Modules\Country\Models\Region;
use Modules\School\Models\School;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('username')->unique();
            $table->string('password');
            $table->string('image')->nullable();
            $table->string('verify_code')->nullable();
            $table->enum('type', ['student', 'teacher']);
            $table->foreignIdFor(School::class)->index()->constrained()->restrictOnDelete();
            $table->foreignIdFor(Country::class)->index()->constrained()->restrictOnDelete();
            $table->foreignIdFor(City::class)->index()->constrained()->restrictOnDelete();
            $table->foreignIdFor(Region::class)->index()->constrained()->restrictOnDelete();
            $table->rememberToken();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
