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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('code', 30)->unique();
            $table->string('name', 150);
            $table->string('customer_type', 50)->default('Distributor'); // Distributor, Wholesaler, Retailer, Direct Client, Exporter
            $table->string('contact_person', 100)->nullable();
            $table->string('phone', 25)->nullable();
            $table->string('email', 150)->nullable();
            $table->string('gstin', 20)->nullable();
            $table->string('pan', 20)->nullable();
            $table->text('address')->nullable();
            $table->string('city', 100)->nullable();
            $table->string('state', 100)->default('Rajasthan');
            $table->string('pincode', 15)->nullable();
            $table->decimal('credit_limit', 12, 2)->default(300000.00);
            $table->string('payment_terms', 50)->default('30 Days');
            $table->decimal('opening_balance', 12, 2)->default(0.00);
            $table->decimal('current_balance', 12, 2)->default(0.00);
            $table->string('bank_name', 100)->nullable();
            $table->string('bank_account_no', 50)->nullable();
            $table->string('bank_ifsc', 25)->nullable();
            $table->string('bank_branch', 100)->nullable();
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
        Schema::dropIfExists('customers');
    }
};
