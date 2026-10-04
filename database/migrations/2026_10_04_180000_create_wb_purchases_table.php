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
        Schema::create('wb_purchases', function (Blueprint $table) {
            $table->id();
            $table->string('slip_no', 30)->unique();
            $table->date('entry_date');
            $table->foreignId('vendor_id')->constrained('vendors')->cascadeOnDelete();
            $table->foreignId('broker_id')->nullable()->constrained('brokers')->nullOnDelete();
            $table->string('order_type', 50)->default('Medium');
            $table->string('vehicle_no', 50)->nullable();
            $table->string('driver_name', 100)->nullable();
            $table->string('driver_phone', 25)->nullable();
            $table->decimal('gross_weight', 12, 3)->default(0.000);
            $table->decimal('tare_weight', 12, 3)->default(0.000);
            $table->decimal('deduction_weight', 12, 3)->default(0.000);
            $table->decimal('net_weight', 12, 3)->default(0.000);
            $table->decimal('total_amount', 14, 2)->default(0.00);
            $table->string('payment_status', 30)->default('unpaid'); // unpaid, paid
            $table->string('payment_mode', 50)->default('Cash'); // Cash, Bank Transfer, Mandi Slip, Cheque
            $table->foreignId('company_id')->nullable()->constrained('companies')->nullOnDelete();
            $table->enum('status', ['received', 'completed', 'cancelled'])->default('received');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('wb_purchase_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wb_purchase_id')->constrained('wb_purchases')->cascadeOnDelete();
            $table->foreignId('item_id')->constrained('items')->cascadeOnDelete();
            $table->string('batch_no', 50)->nullable();
            $table->string('hsn_code', 20)->nullable();
            $table->string('unit', 20)->default('KG');
            $table->decimal('quantity', 12, 3)->default(0.000); // weight in unit
            $table->decimal('rate', 12, 2)->default(0.00); // WB Mandi rate
            $table->decimal('amount', 12, 2)->default(0.00);
            $table->string('notes', 255)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wb_purchase_items');
        Schema::dropIfExists('wb_purchases');
    }
};
