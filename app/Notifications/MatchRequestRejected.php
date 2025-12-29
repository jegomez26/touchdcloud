<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\MatchRequest;

class MatchRequestRejected extends Notification
{
    use Queueable;

    protected $matchRequest;

    /**
     * Create a new notification instance.
     */
    public function __construct(MatchRequest $matchRequest)
    {
        $this->matchRequest = $matchRequest;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $rejecterName = $this->matchRequest->receiverUser->full_name;
        
        return (new MailMessage)
                    ->subject('Match Request Update')
                    ->greeting('Hello!')
                    ->line("Your match request to {$rejecterName} was not accepted at this time.")
                    ->line('Don\'t worry - you can continue searching for other potential matches.')
                    ->action('Find More Matches', url('/dashboard'))
                    ->line('Keep exploring to find your perfect match!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'match_request_rejected',
            'match_request_id' => $this->matchRequest->id,
            'rejecter_name' => $this->matchRequest->receiverUser->full_name,
            'rejecter_id' => $this->matchRequest->receiver_user_id,
            'created_at' => $this->matchRequest->responded_at,
        ];
    }
}