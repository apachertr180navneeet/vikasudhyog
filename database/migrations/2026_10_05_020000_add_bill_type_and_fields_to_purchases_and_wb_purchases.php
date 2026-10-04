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
        Schema::table('purchases', function (Blueprint $table) {
            if (!Schema::hasColumn('purchases', 'bill_type')) {
                $table->string('bill_type', 30)->default('with_bill')->after('purchase_no');
            }
        });

        Schema::table('wb_purchases', function (Blueprint $table) {
            if (!Schema::hasColumn('wb_purchases', 'bill_type')) {
                $table->string('bill_type', 30)->default('without_bill')->after('slip_no');
            }
            if (!Schema::hasColumn('wb_purchases', 'invoice_no')) {
                $table->string('invoice_no', 50)->nullable()->after('bill_type');
            }
            if (!Schema::hasColumn('wb_purchases', 'payment_terms')) {
                $table->string('payment_terms', 50)->default('30 Days')->after('vehicle_no');
            }
            if (!Schema::hasColumn('wb_purchases', 'paid_amount')) {
                $table->decimal('paid_amount', 14, 2)->default(0.00)->after('total_amount');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchases', function (Blueprint $table) {
            if (Schema::hasColumn('purchases', 'bill_type')) {
                $table->dropColumn('bill_type');
            }
        });

        Schema::table('wb_purchases', function (Blueprint $table) {
            $colsToDrop = [];
            foreach (['bill_type', 'invoice_no', 'payment_terms', 'paid_amount'] as $col) {
                if (Schema::hasColumn('wb_purchases', $col)) {
                    $colsToDrop[] = $col;
                }
            }
            if (!empty($colsToDrop)) {
                $table->dropColumn($colsToDrop);
            }
        });
    }
};
