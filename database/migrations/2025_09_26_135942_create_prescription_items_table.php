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
    Schema::create('prescription_items', function (Blueprint $table) {
        $table->id();
        $table->foreignId('prescription_id')->constrained('prescriptions')->cascadeOnDelete();
        $table->foreignId('medicine_id')->constrained('medicines')->cascadeOnDelete();
        $table->integer('quantity');
        $table->string('dosage'); // e.g. 2 tablets
        $table->string('frequency'); // e.g. Twice a day
        $table->integer('duration_days');
        $table->enum('status', ['pending', 'approved', 'dispensed'])->default('pending');
        $table->timestamps();
    });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prescription_items');
    }
};
