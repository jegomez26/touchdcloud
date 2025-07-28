<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;
use App\Models\Conversation;
use App\Models\Participant;
use App\Models\User; // Already there, just good to be explicit
use App\Models\SupportCoordinator; // Import the SupportCoordinator model
use App\Models\Provider; // Import the SupportCoordinator model
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\Builder; // Already there

class ParticipantMessageController extends Controller
{
    // Route: POST /participant/{conversationId}/reply-to-coordinator
    public function replyToCoordinator(Request $request, $conversationId)
    {
        $sender = Auth::user();

        if ($sender->role !== 'participant') {
            return response()->json(['message' => 'Unauthorized: Only participants can reply to support coordinators.'], 403);
        }

        $participant = $sender->participant;
        if (!$participant) { /* handle error */ }

        // Find the conversation
        $conversation = Conversation::where('id', $conversationId)
            ->where('type', 'sc_to_participant')
            ->where('participant_id', $participant->id) // Ensure this participant belongs to this conversation
            ->first();

        if (!$conversation) {
            return response()->json(['message' => 'Conversation not found or you are not part of it.'], 404);
        }

        // Get the support coordinator's user from the conversation
        $supportCoordinatorUser = $conversation->supportCoordinator->user; // Assuming supportCoordinator has a user() relationship

        $conversation->update(['last_message_at' => now()]);

        $message = $conversation->messages()->create([
            'sender_id' => $sender->id,
            'receiver_id' => $supportCoordinatorUser->id,
            'content' => $request->message_body,
            'type' => 'text',
            'original_sender_role' => 'participant',
            'original_recipient_role' => 'coordinator',
        ]);

        return response()->json(['message' => 'Reply sent successfully!', 'data' => $message], 200);
    }
}