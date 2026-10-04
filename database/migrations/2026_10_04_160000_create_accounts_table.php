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
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->string('code', 30)->unique();
            $table->string('name', 150);
            $table->string('account_group', 50); // Bank Accounts, Cash in Hand, Direct Expenses, Indirect Expenses, Direct Incomes, Indirect Incomes, Current Assets, Current Liabilities, Duties & Taxes, Fixed Assets
            $table->decimal('opening_balance', 15, 2)->default(0.00);
            $table->decimal('current_balance', 15, 2)->default(0.00);
            $table->string('balance_type', 10)->default('debit'); // debit (Dr) / credit (Cr)
            $table->foreignId('company_id')->nullable()->constrained('companies')->nullOnDelete();

            // Banking details
            $table->string('bank_name', 100)->nullable();
            $table->string('account_number', 50)->nullable();
            $table->string('ifsc_code', 20)->nullable();
            $table->string('branch_name', 100)->nullable();
            $table->string('upi_id', 100)->nullable();

            $table->text('notes')->nullable();
            $table->string('status', 20)->default('active'); // active / inactive
            $table->timestamps();
            $table->softDeletes();

            $table->index('status');
            $table->index('account_group');
            $table->index('company_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};
