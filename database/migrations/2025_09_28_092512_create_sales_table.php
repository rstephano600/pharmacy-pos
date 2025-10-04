<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pharmacy_id')->constrained('pharmacies')->cascadeOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
            $table->foreignId('sold_by')->constrained('users'); // REMOVED cascadeOnDelete to preserve sales history
            $table->foreignId('prescription_id')->nullable()->constrained('prescriptions')->nullOnDelete(); // ADDED: Link to prescriptions
            
            $table->string('invoice_number')->unique();
            $table->date('sale_date');
            $table->time('sale_time'); 
            
            $table->decimal('subtotal', 12, 4);
            $table->decimal('tax_rate', 5, 2)->default(0); 
            $table->decimal('tax_amount', 12, 4)->default(0); 
            $table->decimal('discount_rate', 5, 2)->default(0); 
            $table->decimal('discount_amount', 12, 4)->default(0); 
            $table->decimal('total_amount', 12, 4); 
            
            $table->enum('status', ['pending', 'completed', 'cancelled', 'refunded'])->default('pending'); // ADDED: refunded status
            $table->enum('payment_method', ['cash', 'insurance', 'mobile', 'card', 'bank_transfer'])->nullable(); // CHANGED: Use enum for consistency
            $table->string('payment_reference')->nullable(); 
            
            $table->text('notes')->nullable(); 
            $table->timestamp('completed_at')->nullable(); 
            
            $table->timestamps();

            $table->index(['pharmacy_id', 'sale_date']);
            $table->index(['customer_id', 'sale_date']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};

