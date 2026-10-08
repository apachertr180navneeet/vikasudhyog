
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
        Schema::create('stock_adjustments', function (Blueprint $table) {
            $table->id();
            $table->string('adjustment_no')->unique();
            $table->date('adjustment_date');
            $table->foreignId('item_id')->constrained('items')->onDelete('cascade');
            $table->enum('type', ['add', 'reduce'])->default('add'); // 'add' = + Surplus/Inward, 'reduce' = - Damage/Shortage/Wastage
            $table->decimal('quantity', 12, 2);
            $table->decimal('previous_stock', 12, 2)->default(0.00);
            $table->decimal('new_stock', 12, 2)->default(0.00);
            $table->string('unit', 30)->nullable();
            $table->decimal('rate', 12, 2)->default(0.00);
            $table->decimal('total_value', 14, 2)->default(0.00);
            $table->string('reason', 150)->default('Physical Audit Reconciliation');
            $table->text('notes')->nullable();
            $table->string('audited_by', 100)->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('company_id')->nullable()->constrained('companies')->nullOnDelete();
            $table->string('status', 30)->default('completed');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_adjustments');
    }
};
