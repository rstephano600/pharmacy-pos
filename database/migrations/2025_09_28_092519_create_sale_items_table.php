<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
       Schema::create('sale_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')->constrained('sales')->cascadeOnDelete();
            $table->foreignId('medicine_id')->constrained('medicines');
            $table->foreignId('medicine_batch_id')->constrained('medicine_batches')->cascadeOnDelete();
            
            $table->integer('quantity');
            $table->decimal('unit_price', 12, 4); 
            $table->decimal('unit_cost', 12, 4); 
            $table->decimal('total_price', 12, 4); 
            $table->decimal('discount_amount', 12, 4)->default(0); 
            
            $table->text('dosage_instructions')->nullable();
            $table->date('expiry_date_at_sale'); 
            
            $table->timestamps();

            $table->index(['sale_id', 'medicine_id']);
            $table->index('medicine_batch_id');
        });

        // Add CHECK constraints using raw SQL
        DB::statement('ALTER TABLE sale_items ADD CONSTRAINT chk_quantity_positive CHECK (quantity > 0)');
        DB::statement('ALTER TABLE sale_items ADD CONSTRAINT chk_unit_price_nonnegative CHECK (unit_price >= 0)');
    }

    public function down(): void
    {
        Schema::dropIfExists('sale_items');
    }
};
