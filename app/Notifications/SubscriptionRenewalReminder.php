<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Subscription;

class SubscriptionRenewalReminder extends Notification implements ShouldQueue
{
    use Queueable;

    protected $subscription;
    protected $isAutoRenew;

    /**
     * Create a new notification instance.
     */
    public function __construct(Subscription $subscription, bool $isAutoRenew = true)
    {
        $this->subscription = $subscription;
        $this->isAutoRenew = $isAutoRenew;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $renewalDate = $this->subscription->ends_at ? $this->subscription->ends_at->format('F j, Y') : 'Soon';
        $planName = $this->subscription->plan_name;
        $amount = '$' . number_format($this->subscription->price, 2);
        
        if ($this->isAutoRenew) {
            $subject = "Subscription Auto-Renewal Reminder - {$planName}";
            $greeting = "Your subscription will be automatically renewed";
            $message = "Your {$planName} subscription will be automatically renewed on {$renewalDate} for {$amount}.";
            $actionText = "Manage Subscription";
            $actionUrl = route('provider.billing');
        } else {
            $subject = "Subscription Renewal Required - {$planName}";
            $greeting = "Your subscription needs to be renewed";
            $message = "Your {$planName} subscription expires on {$renewalDate}. Please renew to continue using our services.";
            $actionText = "Renew Now";
            $actionUrl = route('provider.billing');
        }

        return (new MailMessage)
            ->subject($subject)
            ->greeting($greeting)
            ->line($message)
            ->line('Thank you for using SIL Match!')
            ->action($actionText, $actionUrl)
            ->line('If you have any questions, please contact our support team.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'subscription_id' => $this->subscription->id,
            'plan_name' => $this->subscription->plan_name,
            'renewal_date' => $this->subscription->ends_at,
            'is_auto_renew' => $this->isAutoRenew,
        ];
    }
}
