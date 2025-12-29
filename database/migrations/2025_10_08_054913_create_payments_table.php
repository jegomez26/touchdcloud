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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('subscription_id')->nullable()->constrained()->onDelete('set null');
            $table->string('payment_intent_id')->unique(); // Stripe payment intent ID
            $table->string('status'); // succeeded, failed, pending, canceled
            $table->decimal('amount', 10, 2); // Amount in cents
            $table->string('currency', 3)->default('AUD');
            $table->string('description');
            $table->json('metadata')->nullable(); // Additional payment data
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            $table->string('failure_reason')->nullable();
            $table->string('invoice_url')->nullable(); // Link to invoice PDF
            $table->string('receipt_url')->nullable(); // Link to receipt
            $table->timestamps();
            
            $table->index(['user_id', 'status']);
            $table->index(['subscription_id', 'status']);
            $table->index('paid_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
