<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\MatchRequest;

class MatchRequestAccepted extends Notification
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
        $accepterName = $this->matchRequest->receiverUser->full_name;
        
        return (new MailMessage)
                    ->subject('Match Request Accepted!')
                    ->greeting('Great news!')
                    ->line("Your match request has been accepted by {$accepterName}.")
                    ->line('You can now start a conversation with them.')
                    ->action('Start Conversation', url('/dashboard'))
                    ->line('Log in to begin chatting!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'match_request_accepted',
            'match_request_id' => $this->matchRequest->id,
            'accepter_name' => $this->matchRequest->receiverUser->full_name,
            'accepter_id' => $this->matchRequest->receiver_user_id,
            'conversation_id' => $this->matchRequest->conversation_id ?? null,
            'created_at' => $this->matchRequest->responded_at,
        ];
    }
}