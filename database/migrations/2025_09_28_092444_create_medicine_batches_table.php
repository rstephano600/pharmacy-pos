<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('medicine_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('medicine_id')->constrained('medicines')->cascadeOnDelete();
            $table->foreignId('purchase_order_item_id')->nullable()->constrained('purchase_order_items')->nullOnDelete();
            $table->foreignId('pharmacy_id')->constrained('pharmacies')->cascadeOnDelete();
            
            $table->string('batch_number')->nullable();
            $table->date('manufacture_date')->nullable();
            $table->date('expiry_date')->nullable();
            
            $table->integer('quantity_received');
            $table->integer('quantity_available');
            
            $table->decimal('unit_cost', 12, 4);
            $table->decimal('selling_price', 12, 4)->nullable();
            
            $table->foreignId('received_by')->constrained('users');
            $table->boolean('is_expired')->default(false);
            $table->timestamps();
            
            $table->index(['medicine_id', 'expiry_date']);
            $table->index(['pharmacy_id', 'medicine_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medicine_batches');
    }
};

