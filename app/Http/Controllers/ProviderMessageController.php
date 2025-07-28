<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;
use App\Models\Conversation;
use App\Models\Participant;
use App\Models\User;
use App\Models\SupportCoordinator; // Make sure to import
use App\Models\Provider; // Make sure to import
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class ProviderMessageController extends Controller
{
    // ... (other methods like sendMessageToCoordinator if needed)

    /**
     * Providers send messages to Support Coordinators regarding a specific Participant.
     * This conversation is NOT visible to the Participant.
     * The conversation type will be 'provider_to_sc', but linked to a participant.
     */
    // Route: POST /provider/{participantId}/send-message-to-coordinator-about-participant
    public function sendMessageToCoordinatorAboutParticipant(Request $request, $participantId)
    {
        try {
            $request->validate([
                'message_body' => 'required|string',
            ]);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }

        $sender = Auth::user();

        // 1. Authenticate Sender's Role
        if ($sender->role !== 'provider') {
            return response()->json(['message' => 'Unauthorized: Only providers can send messages via this endpoint.'], 403);
        }

        $provider = $sender->provider;
        if (!$provider) {
            return response()->json(['message' => 'Provider profile not found for the authenticated user.'], 403);
        }

        // 2. Identify the target Participant and their User
        $participant = Participant::with('user')->find($participantId);
        if (!$participant || !$participant->user) {
            return response()->json(['message' => 'Target Participant or associated user not found.'], 404);
        }

        // 3. Determine the Support Coordinator for this Participant
        // This assumes a participant has ONE support coordinator assigned.
        $supportCoordinatorForParticipant = $participant->supportCoordinator; // Assuming participant has a hasOne relationship to SupportCoordinator

        if (!$supportCoordinatorForParticipant || !$supportCoordinatorForParticipant->user) {
            return response()->json(['message' => 'No support coordinator found for this participant to message.'], 404);
        }

        $receiverUser = $supportCoordinatorForParticipant->user; // The SC's User model

        // 4. Find or Create the 'provider_to_sc' Conversation related to this specific participant
        $conversation = Conversation::where('type', 'provider_to_sc')
            ->where('provider_id', $provider->id)
            ->where('support_coordinator_id', $supportCoordinatorForParticipant->id)
            ->where('participant_id', $participant->id) // Crucial: Link to the specific participant
            ->first();

        if (!$conversation) {
            $conversation = Conversation::create([
                'type' => 'provider_to_sc',
                'provider_id' => $provider->id,
                'support_coordinator_id' => $supportCoordinatorForParticipant->id,
                'participant_id' => $participant->id, // Store the participant ID to tie this conversation to them
                'last_message_at' => now(),
            ]);
        } else {
            $conversation->update(['last_message_at' => now()]);
        }

        // 5. Create the Message
        $message = $conversation->messages()->create([
            'sender_id' => $sender->id, // Provider's User ID
            'receiver_id' => $receiverUser->id, // Support Coordinator's User ID
            'content' => $request->message_body,
            'type' => 'text',
            'original_sender_role' => 'provider',
            'original_recipient_role' => 'coordinator', // The direct recipient is the coordinator
        ]);

        return response()->json([
            'message' => 'Message sent to coordinator about participant successfully!',
            'data' => $message,
            'conversation' => $conversation
        ], 200);
    }
}