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
        Schema::create('whatsapp_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained('companies')->nullOnDelete();
            $table->string('provider')->default('meta_cloud_api'); // meta_cloud_api, twilio, gupshup, sandbox
            $table->string('phone_number_id')->nullable();
            $table->string('waba_id')->nullable();
            $table->text('access_token')->nullable();
            $table->string('display_phone_number')->nullable()->default('+91 98290 12345');
            $table->string('webhook_verify_token')->nullable()->default('vu_wa_verify_token_2026');
            $table->boolean('is_enabled')->default(true);
            $table->boolean('sandbox_mode')->default(true);
            $table->unsignedInteger('monthly_quota')->default(5000);
            $table->unsignedInteger('credits_used')->default(4825);
            
            // Automation Triggers
            $table->boolean('auto_send_invoice')->default(true);
            $table->boolean('auto_send_order')->default(true);
            $table->boolean('auto_send_dispatch')->default(true);
            $table->boolean('auto_send_receipt')->default(true);
            $table->boolean('auto_send_due_reminder')->default(false);

            // Message Templates
            $table->text('invoice_template')->nullable();
            $table->text('order_template')->nullable();
            $table->text('dispatch_template')->nullable();
            $table->text('receipt_template')->nullable();
            $table->text('due_reminder_template')->nullable();

            $table->timestamps();
        });

        Schema::create('whatsapp_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained('companies')->nullOnDelete();
            $table->string('recipient_name')->nullable();
            $table->string('recipient_phone', 30);
            $table->string('template_type', 50)->default('custom'); // invoice, order, dispatch, receipt, due_reminder, test, custom
            $table->text('message_body');
            $table->string('status', 30)->default('sent'); // sent, delivered, read, failed, simulated
            $table->string('response_id')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            $table->index(['template_type', 'status']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whatsapp_logs');
        Schema::dropIfExists('whatsapp_settings');
    }
};
