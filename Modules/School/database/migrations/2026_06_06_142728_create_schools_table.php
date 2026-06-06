<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Country\Models\City;
use Modules\Country\Models\Country;
use Modules\Country\Models\Region;
use Modules\Tenant\Models\Tenant;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('schools', function (Blueprint $table) {
            $table->id();
            $table->json('title');
            $table->string('phone')->nullable();
            $table->string('email')->nullable()->unique();
            $table->foreignIdFor(Country::class)->index()->constrained()->restrictOnDelete();
            $table->foreignIdFor(City::class)->index()->constrained()->restrictOnDelete();
            $table->foreignIdFor(Region::class)->index()->constrained()->restrictOnDelete();
            $table->string('tenant_id');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schools');
    }
};
