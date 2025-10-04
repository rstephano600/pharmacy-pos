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
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pharmacy_id')->constrained('pharmacies')->cascadeOnDelete();
            $table->foreignId('medicine_id')->constrained('medicines')->cascadeOnDelete();
            $table->foreignId('medicine_batch_id')->nullable()->constrained('medicine_batches')->cascadeOnDelete();
            
            $table->enum('movement_type', ['receipt', 'sale', 'adjustment', 'expiry', 'return', 'transfer']);
            $table->integer('quantity_change');
            $table->integer('quantity_before');
            $table->integer('quantity_after');
            
            $table->string('reference_type')->nullable();
            $table->unsignedBigInteger('reference_id')->nullable(); 
            
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users');
            
            $table->timestamps();
            
            $table->index(['pharmacy_id', 'medicine_id', 'created_at']);
            $table->index(['reference_type', 'reference_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
