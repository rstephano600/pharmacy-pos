
<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pharmacy_id')->constrained('pharmacies')->cascadeOnDelete();
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers');
            $table->string('po_number')->unique();
            $table->date('order_date');
            $table->date('expected_delivery_date')->nullable();
            $table->enum('status', ['pending','partial','received','cancelled'])->default('pending');
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->foreignId('ordered_by')->constrained('users');
            $table->string('payment_terms')->nullable();
            $table->text('notes')->nullable();
            $table->json('delivery_address')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_orders');
    }
};
