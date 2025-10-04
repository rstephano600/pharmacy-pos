<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pharmacy_stocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pharmacy_id')->constrained('pharmacies')->cascadeOnDelete();
            $table->foreignId('medicine_id')->constrained('medicines')->cascadeOnDelete();

            $table->integer('total_quantity')->default(0);
            $table->integer('available_quantity')->default(0);
            $table->integer('reserved_quantity')->default(0); 
            $table->integer('minimum_stock_level')->default(0); 

            $table->decimal('average_cost', 12, 4)->default(0); 
            $table->decimal('default_selling_price', 12, 4)->nullable(); 

            $table->timestamps();

            $table->unique(['pharmacy_id', 'medicine_id']);
        });

        // Add CHECK constraints manually with raw SQL
        DB::statement('ALTER TABLE pharmacy_stocks ADD CONSTRAINT chk_available_quantity CHECK (available_quantity >= 0)');
        DB::statement('ALTER TABLE pharmacy_stocks ADD CONSTRAINT chk_total_vs_available CHECK (total_quantity >= available_quantity)');
    }

    public function down(): void
    {
        Schema::dropIfExists('pharmacy_stocks');
    }
};
