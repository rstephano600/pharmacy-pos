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
     Schema::create('medicines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pharmacy_id')->constrained('pharmacies');
            $table->string('name');
            $table->string('generic_name')->nullable();
            $table->string('brand_name')->nullable();
            $table->foreignId('category_id')->constrained('medicine_categories');
            $table->string('strength')->nullable();
            $table->string('form');
            $table->string('prescription_type');
            $table->string('storage_type');
            $table->string('barcode')->nullable()->unique();
            $table->decimal('unit_price', 10, 2);
            $table->integer('reorder_level')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['category_id', 'is_active']);
            $table->fullText(['name', 'generic_name', 'brand_name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medicines');
    }
};
