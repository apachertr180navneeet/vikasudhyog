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
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->string('purchase_no', 30)->unique();
            $table->string('invoice_no', 50)->nullable();
            $table->date('invoice_date');
            $table->foreignId('vendor_id')->constrained('vendors')->cascadeOnDelete();
            $table->foreignId('broker_id')->nullable()->constrained('brokers')->nullOnDelete();
            $table->string('order_type', 50)->default('Medium'); // Medium, Urgent, Fast, Ready Delivery
            $table->string('vehicle_no', 50)->nullable();
            $table->string('payment_terms', 50)->default('30 Days');
            $table->decimal('subtotal', 14, 2)->default(0.00);
            $table->decimal('tax_amount', 14, 2)->default(0.00);
            $table->decimal('bill_total', 14, 2)->default(0.00);
            $table->decimal('under_billing_total', 14, 2)->default(0.00);
            $table->decimal('round_off', 8, 2)->default(0.00);
            $table->decimal('grand_total', 14, 2)->default(0.00);
            $table->decimal('paid_amount', 14, 2)->default(0.00);
            $table->string('payment_status', 30)->default('unpaid'); // unpaid, partial, paid
            $table->foreignId('company_id')->nullable()->constrained('companies')->nullOnDelete();
            $table->enum('status', ['received', 'completed', 'cancelled'])->default('received');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('purchase_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_id')->constrained('purchases')->cascadeOnDelete();
            $table->foreignId('item_id')->constrained('items')->cascadeOnDelete();
            $table->string('batch_no', 50)->nullable();
            $table->string('hsn_code', 20)->nullable();
            $table->string('unit', 20)->default('KG');
            $table->decimal('quantity', 12, 3)->default(0.000);
            $table->decimal('actual_rate', 12, 2)->default(0.00);
            $table->decimal('bill_rate', 12, 2)->default(0.00);
            $table->decimal('ub_rate', 12, 2)->default(0.00);
            $table->decimal('gst_percent', 5, 2)->default(5.00);
            $table->decimal('tax_amount', 12, 2)->default(0.00);
            $table->decimal('bill_amount', 12, 2)->default(0.00);
            $table->decimal('under_amount', 12, 2)->default(0.00);
            $table->decimal('total_amount', 12, 2)->default(0.00);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_items');
        Schema::dropIfExists('purchases');
    }
};
