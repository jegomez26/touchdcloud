<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Payment;
use App\Models\User;
use App\Models\Subscription;
use Carbon\Carbon;

class PaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get users with provider role
        $providers = User::where('role', 'provider')->get();
        
        if ($providers->isEmpty()) {
            $this->command->info('No providers found. Please create providers first.');
            return;
        }

        foreach ($providers as $provider) {
            // Get or create a subscription for the provider
            $subscription = Subscription::where('user_id', $provider->id)->first();
            
            if (!$subscription) {
                // Create a sample subscription
                $subscription = Subscription::create([
                    'user_id' => $provider->id,
                    'plan_id' => 2, // Assuming Growth plan exists
                    'name' => 'Growth Plan',
                    'plan_name' => 'Growth Plan',
                    'plan_slug' => 'growth',
                    'billing_period' => 'monthly',
                    'price' => 599.00,
                    'participant_profile_limit' => 50,
                    'accommodation_listing_limit' => 25,
                    'stripe_status' => 'active',
                    'paypal_status' => 'active',
                    'starts_at' => now()->subMonths(3),
                ]);
            }

            // Create sample payments for the last 3 months
            for ($i = 0; $i < 3; $i++) {
                $paymentDate = now()->subMonths($i);
                
                Payment::create([
                    'user_id' => $provider->id,
                    'subscription_id' => $subscription->id,
                    'payment_intent_id' => 'pi_sim_' . uniqid(),
                    'status' => 'succeeded',
                    'amount' => $subscription->price,
                    'currency' => 'AUD',
                    'description' => $subscription->plan_name . ' - ' . ucfirst($subscription->billing_period),
                    'metadata' => [
                        'plan_id' => $subscription->plan_id,
                        'billing_period' => $subscription->billing_period,
                    ],
                    'paid_at' => $paymentDate,
                    'invoice_url' => '/invoices/invoice_' . uniqid() . '.pdf',
                    'receipt_url' => '/receipts/receipt_' . uniqid() . '.pdf',
                ]);
            }

            // Create one failed payment for demonstration
            Payment::create([
                'user_id' => $provider->id,
                'subscription_id' => $subscription->id,
                'payment_intent_id' => 'pi_failed_' . uniqid(),
                'status' => 'failed',
                'amount' => $subscription->price,
                'currency' => 'AUD',
                'description' => $subscription->plan_name . ' - ' . ucfirst($subscription->billing_period),
                'metadata' => [
                    'plan_id' => $subscription->plan_id,
                    'billing_period' => $subscription->billing_period,
                ],
                'failed_at' => now()->subDays(15),
                'failure_reason' => 'insufficient_funds',
            ]);
        }

        $this->command->info('Sample payments created successfully!');
    }
}
