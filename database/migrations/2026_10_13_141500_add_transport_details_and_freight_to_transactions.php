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
                if (!Schema::hasColumn($table, 'transport_name')) {
                    $tableBlueprint->string('transport_name', 150)->nullable()->after('order_type');
                }
                if (!Schema::hasColumn($table, 'lr_no')) {
                    $tableBlueprint->string('lr_no', 50)->nullable()->after('vehicle_no');
                }
                if (!Schema::hasColumn($table, 'freight_amount')) {
                    $tableBlueprint->decimal('freight_amount', 14, 2)->default(0.00)->after('payment_terms');
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
                $columns = ['transport_name', 'lr_no', 'freight_amount'];
                foreach ($columns as $column) {
                    if (Schema::hasColumn($table, $column)) {
                        $tableBlueprint->dropColumn($column);
                    }
                }
            });
        }
    }
};
