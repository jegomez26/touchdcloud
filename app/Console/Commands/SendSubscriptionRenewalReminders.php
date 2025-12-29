<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Subscription;
use App\Notifications\SubscriptionRenewalReminder;
use Carbon\Carbon;

class SendSubscriptionRenewalReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscriptions:send-renewal-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send renewal reminder emails to users whose subscriptions are expiring soon';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting subscription renewal reminder process...');

        // Get subscriptions expiring in 7 days
        $expiringSoon = Subscription::where('ends_at', '<=', Carbon::now()->addDays(7))
            ->where('ends_at', '>', Carbon::now())
            ->where(function($query) {
                $query->where('stripe_status', 'active')
                      ->orWhere('paypal_status', 'active');
            })
            ->with('user')
            ->get();

        $this->info("Found {$expiringSoon->count()} subscriptions expiring soon.");

        foreach ($expiringSoon as $subscription) {
            try {
                // Send reminder based on auto-renewal setting
                $subscription->user->notify(
                    new SubscriptionRenewalReminder($subscription, $subscription->auto_renew)
                );

                $this->line("Sent reminder to {$subscription->user->email} for {$subscription->plan_name}");
            } catch (\Exception $e) {
                $this->error("Failed to send reminder to {$subscription->user->email}: {$e->getMessage()}");
            }
        }

        // Get trial subscriptions ending in 3 days
        $trialsEndingSoon = Subscription::where('trial_ends_at', '<=', Carbon::now()->addDays(3))
            ->where('trial_ends_at', '>', Carbon::now())
            ->where(function($query) {
                $query->where('stripe_status', 'trialing')
                      ->orWhere('paypal_status', 'trialing');
            })
            ->with('user')
            ->get();

        $this->info("Found {$trialsEndingSoon->count()} trials ending soon.");

        foreach ($trialsEndingSoon as $subscription) {
            try {
                // Send trial ending reminder
                $subscription->user->notify(
                    new SubscriptionRenewalReminder($subscription, false)
                );

                $this->line("Sent trial ending reminder to {$subscription->user->email} for {$subscription->plan_name}");
            } catch (\Exception $e) {
                $this->error("Failed to send trial reminder to {$subscription->user->email}: {$e->getMessage()}");
            }
        }

        $this->info('Subscription renewal reminder process completed.');
    }
}
