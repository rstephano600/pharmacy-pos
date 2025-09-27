<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
Schema::create('customers', function (Blueprint $table) {
    $table->id();
    $table->foreignId('pharmacy_id')->constrained()->cascadeOnDelete(); // multi-pharmacy scope
    $table->string('name');
    $table->string('phone')->nullable();
    $table->string('email')->nullable()->unique();
    $table->enum('customer_type', ['individual', 'hospital', 'clinic','other'])->default('individual');
    $table->string('insurance_provider')->nullable();
    $table->string('insurance_number')->nullable();
    $table->json('address')->nullable(); // street, city, region, etc.
    $table->json('demographics')->nullable(); // age, gender, etc.
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
