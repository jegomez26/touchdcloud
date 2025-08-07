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
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            // Core subscription plan details
            $table->string('name'); // Corresponds to 'default' or a specific name if multiple subscriptions per user are allowed by gateway (e.g., "main" for primary plan, "featured_placement_addon")
            $table->string('plan_name'); // e.g., 'Starter Plan', 'Growth Plan', 'Premium Plan'
            $table->string('plan_slug'); // e.g., 'starter', 'growth', 'premium'
            $table->enum('billing_period', ['monthly', 'yearly']); // Tracks if it's a monthly or yearly subscription
            $table->decimal('price', 10, 2); // Stores the actual price paid (e.g., 299.00, 2988.00)

            // Gateway specific IDs
            $table->string('stripe_id')->nullable(); // Subscription ID from Stripe
            $table->string('stripe_status')->nullable(); // Status from Stripe (active, cancelled, trialing, etc.)
            $table->string('stripe_price_id')->nullable(); // Price ID from Stripe (if using Stripe Billing for specific prices)
            $table->string('paypal_id')->nullable(); // Subscription ID from PayPal
            $table->string('paypal_status')->nullable(); // Status from PayPal
            $table->string('paypal_plan_id')->nullable(); // Plan ID from PayPal

            $table->enum('payment_gateway', ['stripe', 'paypal'])->nullable(); // Make nullable for initial creation if not immediately tied to a gateway
            
            // Feature-related limits and statuses
            $table->integer('participant_profile_limit')->nullable(); // e.g., 3, 10, null (for unlimited 11+)
            $table->boolean('has_advanced_matching_filters')->default(false);
            $table->boolean('has_phone_support')->default(false);
            $table->boolean('has_early_feature_access')->default(false);
            $table->boolean('has_dedicated_support')->default(false);
            $table->boolean('has_custom_onboarding')->default(false);
            $table->boolean('includes_property_listings')->default(false); // For Premium Plan

            // Add-ons (consider a separate 'subscription_addons' table if many complex add-ons)
            $table->boolean('has_featured_placement')->default(false);
            // 'profile writing/editing' might be a one-time service, not a recurring subscription add-on,
            // so it's probably better handled as a separate payment/order.

            // Trial and End Dates
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamp('ends_at')->nullable(); // When the subscription effectively ends (cancelled, expired)
            $table->timestamp('starts_at')->nullable(); // When the subscription started (useful for tracking founding partner offer)

            // Special offer flags
            $table->boolean('is_founding_partner')->default(false); // Flag for the founding partner offer

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