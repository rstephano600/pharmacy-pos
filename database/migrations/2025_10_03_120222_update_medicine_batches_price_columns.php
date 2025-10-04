<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateMedicineBatchesPriceColumns extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('medicine_batches', function (Blueprint $table) {
            // Change columns from decimal to double
            $table->double('unit_cost', 12, 2)->change();
            $table->double('selling_price', 12, 2)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('medicine_batches', function (Blueprint $table) {
            // Revert back to decimal if rollback
            $table->decimal('unit_cost', 12, 4)->change();
            $table->decimal('selling_price', 12, 4)->nullable()->change();
        });
    }
}
