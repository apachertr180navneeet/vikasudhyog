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
        Schema::create('payment_vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('voucher_no', 50)->unique();
            $table->date('voucher_date');
            $table->string('payment_type', 20)->default('Vendor'); // Vendor, Expense
            $table->foreignId('vendor_id')->nullable()->constrained('vendors')->nullOnDelete();
            $table->string('expense_head', 150)->nullable();
            $table->foreignId('account_id')->nullable()->constrained('accounts')->nullOnDelete();
            $table->decimal('amount', 15, 2)->default(0.00);
            $table->string('payment_mode', 30)->default('Cash'); // Cash, Bank Transfer, NEFT, RTGS, Cheque, UPI, Other
            $table->string('reference_no', 100)->nullable();
            $table->date('reference_date')->nullable();
            $table->string('against_invoice', 100)->nullable();
            $table->text('notes')->nullable();
            $table->string('status', 20)->default('active'); // active, cancelled
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_vouchers');
    }
};
