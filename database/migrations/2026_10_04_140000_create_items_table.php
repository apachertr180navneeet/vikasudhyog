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
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->string('code', 30)->unique();
            $table->string('name', 150);
            $table->string('category', 50)->default('Mehndi / Henna'); // Mehndi / Henna, Herbal Powder, Ayurvedic Raw Material, Finished Product, Packaging Material
            $table->string('unit', 20)->default('KG'); // KG, GRAM, BAG, BOX, PACKET, QUINTAL
            $table->string('hsn_code', 20)->nullable();
            $table->decimal('gst_rate', 5, 2)->default(5.00);
            $table->decimal('purchase_rate', 12, 2)->default(0.00);
            $table->decimal('sale_rate', 12, 2)->default(0.00);
            $table->decimal('opening_stock', 12, 2)->default(0.00);
            $table->decimal('current_stock', 12, 2)->default(0.00);
            $table->decimal('min_stock_alert', 12, 2)->default(20.00);
            $table->string('batch_no', 50)->nullable();
            $table->foreignId('company_id')->nullable()->constrained('companies')->nullOnDelete();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
