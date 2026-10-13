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
        $tables = ['purchases', 'sales', 'wb_purchases', 'wb_sales'];

        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $tableBlueprint) use ($table) {
                if (!Schema::hasColumn($table, 'transporter_id')) {
                    $tableBlueprint->foreignId('transporter_id')->nullable()->after('transport_name')->constrained('transporters')->nullOnDelete();
                }
                if (!Schema::hasColumn($table, 'invoice_attachment')) {
                    $tableBlueprint->string('invoice_attachment', 255)->nullable()->after('invoice_no');
                }
                if (!Schema::hasColumn($table, 'is_gst_inclusive')) {
                    $tableBlueprint->boolean('is_gst_inclusive')->default(false)->after('bill_type');
                }
                if (!Schema::hasColumn($table, 'e_way_bill_no')) {
                    $tableBlueprint->string('e_way_bill_no', 50)->nullable()->after('lr_no');
                }
                if (!Schema::hasColumn($table, 'transport_date')) {
                    $tableBlueprint->date('transport_date')->nullable()->after('e_way_bill_no');
                }
                if (!Schema::hasColumn($table, 'transport_notes')) {
                    $tableBlueprint->text('transport_notes')->nullable()->after('transport_date');
                }
                if (!Schema::hasColumn($table, 'advance_amount')) {
                    $tableBlueprint->decimal('advance_amount', 14, 2)->default(0.00)->after('paid_amount');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = ['purchases', 'sales', 'wb_purchases', 'wb_sales'];

        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $tableBlueprint) use ($table) {
                if (Schema::hasColumn($table, 'transporter_id')) {
                    $tableBlueprint->dropConstrainedForeignId('transporter_id');
                }
                $columns = [
                    'invoice_attachment',
                    'is_gst_inclusive',
                    'e_way_bill_no',
                    'transport_date',
                    'transport_notes',
                    'advance_amount',
                ];
                foreach ($columns as $column) {
                    if (Schema::hasColumn($table, $column)) {
                        $tableBlueprint->dropColumn($column);
                    }
                }
            });
        }
    }
};
