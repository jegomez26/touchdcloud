<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel; // Use PrivateChannel for private conversations
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Message $message; // Public property to hold the Message model instance

    /**
     * Create a new event instance.
     */
    public function __construct(Message $message)
    {
        $this->message = $message;
        // Eager load sender and receiver relationships to ensure they are available
        // when the event is serialized for broadcasting.
        $this->message->load(['sender', 'receiver']);
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        // Broadcast to a private channel specific to the conversation ID.
        // The frontend will subscribe to 'conversation.<conversation_id>'.
        return [
            new PrivateChannel('conversation.' . $this->message->conversation_id),
        ];
    }

    /**
     * The event's broadcast name.
     * This is what Laravel Echo will listen for on the frontend.
     * It's good practice to explicitly name it.
     */
    public function broadcastAs(): string
    {
        return 'message.sent'; // This will be the event name on the frontend
    }

    /**
     * Get the data to broadcast.
     * This determines exactly what JSON data the frontend receives.
     */
    public function broadcastWith(): array
    {
        // Prepare the data to be sent to the frontend.
        // Include all necessary fields that your frontend component needs to display the message.
        return [
            'id' => $this->message->id,
            'conversation_id' => $this->message->conversation_id,
            'sender_id' => $this->message->sender_id,
            'receiver_id' => $this->message->receiver_id,
            'content' => $this->message->content,
            'type' => $this->message->type,
            'read_at' => $this->message->read_at?->toIsoString(), // Send as ISO string if not null
            'original_sender_role' => $this->message->original_sender_role,
            'original_recipient_role' => $this->message->original_recipient_role,
            'created_at' => $this->message->created_at->diffForHumans(), // Or any other desired format
            // Include sender's name for display (from the eager loaded 'sender' relationship)
            'sender_name' => $this->message->sender->name ?? 'Unknown Sender',
        ];
    }
}