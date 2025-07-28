<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;
use App\Models\Conversation;
use App\Models\Participant;
use App\Models\User;
use App\Models\SupportCoordinator; // Import the SupportCoordinator model
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class CoordinatorMessageController extends Controller
{
    /**
     * Support Coordinators send messages to Participants.
     * This creates or appends to a 'sc_to_participant' conversation.
     */
    public function sendMessageToParticipant(Request $request, $participantId)
    {
        try {
            $request->validate([
                // 'message_subject' is included here. Your messages table currently doesn't have a 'subject' column.
                // If you want to store a subject per message, you'd need to add it to your `messages` migration.
                // Otherwise, it might be relevant for the Conversation itself (e.g., initial subject of the conversation).
                // For now, it will be validated but not stored in the `messages` table unless you add the column.
                'message_subject' => 'nullable|string|max:255',
                'message_body' => 'required|string',
            ]);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }

        $sender = Auth::user(); // The authenticated user (who should be a Support Coordinator)

        // 1. Authenticate Sender's Role (redundant if middleware handles, but good for explicit check)
        if ($sender->role !== 'coordinator') {
            return response()->json(['message' => 'Unauthorized: Only support coordinators can send messages via this endpoint.'], 403);
        }

        // Get the specific SupportCoordinator instance related to the sender user
        $supportCoordinator = $sender->supportCoordinator;

        if (!$supportCoordinator) {
            // This case should ideally not happen if role is 'coordinator' but no associated supportCoordinator record.
            // It indicates a data inconsistency.
            return response()->json(['message' => 'Support Coordinator profile not found for the authenticated user.'], 403);
        }

        // 2. Find the Participant and their associated User
        $participant = Participant::with('user')->find($participantId);

        if (!$participant || !$participant->user) {
            return response()->json(['message' => 'Participant or associated user not found.'], 404);
        }

        $receiverUser = $participant->user; // The User model associated with the participant

        // 3. Find or Create a Conversation
        // We need to find an existing conversation of type 'sc_to_participant'
        // between the current support coordinator and the target participant.
        $conversation = Conversation::where('type', 'sc_to_participant')
            ->where('support_coordinator_id', $supportCoordinator->id) // Use the ID from the SupportCoordinator instance
            ->where('participant_id', $participant->id)
            ->first();

        if (!$conversation) {
            // Create a new conversation if it doesn't exist
            $conversation = Conversation::create([
                'type' => 'sc_to_participant',
                'support_coordinator_id' => $supportCoordinator->id,
                'participant_id' => $participant->id,
                'last_message_at' => now(), // Set initial last message timestamp
                // If you want the message_subject to be the conversation subject, add it here:
                'subject' => $request->message_subject, // Ensure 'subject' column exists in 'conversations' table and is fillable
            ]);
        } else {
            // Update last_message_at for an existing conversation
            $conversation->update(['last_message_at' => now()]);
        }

        // 4. Create the Message
        $message = $conversation->messages()->create([
            'sender_id' => $sender->id, // The User ID of the Support Coordinator
            'receiver_id' => $receiverUser->id, // The User ID of the Participant
            'content' => $request->message_body,
            'type' => 'text', // Default to 'text', could be dynamic if you allow other types
            'original_sender_role' => 'coordinator', // Use the role from the User model
            'original_recipient_role' => 'participant', // Use the role for clarity
            // If you added a 'subject' column to messages:
            // 'subject' => $request->message_subject,
        ]);

        return response()->json([
            'message' => 'Message sent successfully!',
            'data' => $message,
            'conversation' => $conversation
        ], 200);
    }
}