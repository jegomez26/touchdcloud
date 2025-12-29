<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Provider;
use App\Models\Plan;
use App\Models\Subscription;
use App\Services\SubscriptionService;

class CreateTestSubscription extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscription:create-test {email?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a test subscription for a provider user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        
        if (!$email) {
            // Find the first provider user
            $user = User::where('role', 'provider')->first();
            if (!$user) {
                $this->error('No provider users found. Please create a provider user first.');
                return 1;
            }
        } else {
            $user = User::where('email', $email)->where('role', 'provider')->first();
            if (!$user) {
                $this->error("Provider user with email '{$email}' not found.");
                return 1;
            }
        }

        // Check if user already has a subscription
        $existingSubscription = Subscription::where('user_id', $user->id)->first();
        if ($existingSubscription) {
            $this->info("User {$user->email} already has a subscription. Updating to active status...");
            $existingSubscription->update([
                'stripe_status' => 'active',
                'paypal_status' => 'active',
                'ends_at' => null,
            ]);
            $this->info("Subscription updated successfully!");
            return 0;
        }

        // Get the first available plan
        $plan = Plan::first();
        if (!$plan) {
            $this->error('No plans found. Please create plans first.');
            return 1;
        }

        // Create a test subscription
        $subscription = Subscription::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'name' => $plan->name,
            'plan_name' => $plan->name,
            'plan_slug' => $plan->slug,
            'billing_period' => 'monthly',
            'price' => $plan->monthly_price,
            'participant_profile_limit' => $plan->participant_profile_limit,
            'accommodation_listing_limit' => $plan->accommodation_listing_limit,
            'stripe_status' => 'active',
            'paypal_status' => 'active',
            'starts_at' => now(),
            'ends_at' => null, // No end date for test subscription
            'auto_renew' => true,
        ]);

        $this->info("Test subscription created successfully for {$user->email}!");
        $this->info("Plan: {$plan->name}");
        $this->info("Participant Limit: " . ($plan->participant_profile_limit ?? 'Unlimited'));
        $this->info("Accommodation Limit: " . ($plan->accommodation_listing_limit ?? 'Unlimited'));
        
        return 0;
    }
}