<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop foreign key first
            $table->dropForeign(['pharmacy_id']);

            // Make column nullable
            $table->unsignedBigInteger('pharmacy_id')->nullable()->change();

            // Re-add foreign key (optional, only if you still need it)
            $table->foreign('pharmacy_id')->references('id')->on('pharmacies')->onDelete('set null')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop modified foreign key
            $table->dropForeign(['pharmacy_id']);

            // Revert column to NOT NULL
            $table->unsignedBigInteger('pharmacy_id')->nullable(false)->change();

            // Restore original foreign key
            $table->foreign('pharmacy_id')->references('id')->on('pharmacies')->onDelete('cascade')->nullable();
        });
    }
};
