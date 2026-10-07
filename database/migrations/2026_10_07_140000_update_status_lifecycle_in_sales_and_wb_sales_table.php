<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Modify status column in sales table to string(30) to support: ordered, dispatched, delivered, completed, cancelled
        Schema::table('sales', function (Blueprint $table) {
            $table->string('status', 30)->default('dispatched')->change();
        });

        // Modify status column in wb_sales table to string(30) to support: ordered, dispatched, delivered, completed, cancelled
        Schema::table('wb_sales', function (Blueprint $table) {
            $table->string('status', 30)->default('dispatched')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->enum('status', ['dispatched', 'completed', 'cancelled'])->default('dispatched')->change();
        });

        Schema::table('wb_sales', function (Blueprint $table) {
            $table->enum('status', ['dispatched', 'completed', 'cancelled'])->default('dispatched')->change();
        });
    }
};
