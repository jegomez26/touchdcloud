<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\MatchRequest;

class MatchRequestReceived extends Notification
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
        $senderName = $this->matchRequest->senderUser->full_name;
        
        return (new MailMessage)
                    ->subject('New Match Request Received')
                    ->greeting('Hello!')
                    ->line("You have received a new match request from {$senderName}.")
                    ->line($this->matchRequest->message ? "Message: {$this->matchRequest->message}" : '')
                    ->action('View Request', url('/dashboard'))
                    ->line('Please log in to accept or reject the match request.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'match_request_received',
            'match_request_id' => $this->matchRequest->id,
            'sender_name' => $this->matchRequest->senderUser->full_name,
            'sender_id' => $this->matchRequest->sender_user_id,
            'message' => $this->matchRequest->message,
            'created_at' => $this->matchRequest->created_at,
        ];
    }
}