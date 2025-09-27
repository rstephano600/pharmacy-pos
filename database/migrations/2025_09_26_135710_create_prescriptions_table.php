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
    Schema::create('prescriptions', function (Blueprint $table) {
    $table->id();
    $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
    $table->foreignId('pharmacy_id')->constrained('pharmacies')->cascadeOnDelete();
    $table->string('doctor_name')->nullable();
    $table->string('doctor_license')->nullable();
    $table->string('diagnosis')->nullable();
    $table->text('notes')->nullable();
    $table->enum('status', ['pending', 'verified', 'dispensed', 'rejected'])->default('pending');
    $table->foreignId('created_by')->constrained('users');
    $table->timestamps();
    });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prescriptions');
    }
};
