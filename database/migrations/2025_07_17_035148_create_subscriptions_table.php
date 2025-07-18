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
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Link to user, not just provider
            $table->string('name')->default('default'); // Name for Cashier compatibility
            $table->string('stripe_id')->nullable();
            $table->string('stripe_status')->nullable();
            $table->string('stripe_price')->nullable();
            $table->string('paypal_id')->nullable();
            $table->string('paypal_status')->nullable();
            $table->string('paypal_plan_id')->nullable();
            $table->enum('payment_gateway', ['stripe', 'paypal']);
            $table->string('plan_name'); // 'Basic', 'Standard', 'Advanced'
            $table->string('plan_slug'); // 'basic', 'standard', 'advanced'
            $table->integer('quantity')->nullable();
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
