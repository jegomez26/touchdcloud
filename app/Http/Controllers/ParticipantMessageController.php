<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;
use App\Models\Conversation;
use App\Models\Participant;
use App\Models\User;
use App\Models\SupportCoordinator;
use App\Events\MessageSent;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class ParticipantMessageController extends Controller
{
    /**
     * Display a listing of conversations for the authenticated participant.
     */
    public function index()
    {
        $user = Auth::user();
        $participant = $user->participant;

        if (!$participant) {
            abort(403, 'Participant profile not found.');
        }

        $conversations = Conversation::forParticipant($participant->id)
                                ->with(['supportCoordinator.user', 'messages' => function($query) {
                                    $query->latest()->limit(1);
                                }])
                                ->orderByDesc('last_message_at')
                                ->paginate(10);

        $initialConversationId = $conversations->first() ? $conversations->first()->id : null;

        return view('indiv.messages.index', compact('conversations', 'initialConversationId'));
    }

    /**
     * Display a specific conversation for the participant.
     */
    public function show(Conversation $conversation)
    {
        $user = Auth::user(); // Authenticated Participant's User model
        $participant = $user->participant;

        // Ensure the conversation belongs to the authenticated participant
        if (!$participant || $conversation->participant_id !== $participant->id) {
            abort(403, 'Unauthorized to view this conversation.');
        }

        // Load all messages for this conversation, ordered oldest to newest for chronological display
        // Eager load sender and receiver User models to get their names/roles
        $messages = $conversation->messages()->with('sender', 'receiver')->oldest()->get();

        // Mark messages sent by the support coordinator as read by the participant
        $conversation->messages()
                     ->where('receiver_id', $user->id) // Messages sent TO the current Participant (receiver_id is user.id)
                     ->whereNull('read_at')
                     ->update(['read_at' => now()]);

        // Get the support coordinator associated with THIS conversation
        $supportCoordinator = $conversation->supportCoordinator;
        $coordinatorUser = $supportCoordinator->user; // The User model of the support coordinator

        // Prepare the coordinator's name for the header and messages
        $coordinatorNameForDisplay = $coordinatorUser->name ?? 'Support Coordinator';
        $fullCoordinatorName = $coordinatorNameForDisplay . ' (Support Coordinator)';

        return response()->json([
            'conversation' => $conversation->load('supportCoordinator.user'), // Re-load just in case, though usually not needed after initial load
            'messages' => $messages->map(function($message) use ($user, $fullCoordinatorName, $coordinatorUser) {
                // Map to simplify data for frontend and add a 'is_sender' flag
                $senderName = '';
                if ($message->sender_id === $user->id) {
                    $senderName = 'You'; // Sender is the authenticated participant
                } elseif ($message->sender_id === $coordinatorUser->id) { // Sender is the coordinator for this conversation
                    $senderName = $fullCoordinatorName;
                } else {
                    // Fallback for any other sender, though typically shouldn't happen in a 1-to-1 chat
                    $senderName = $message->sender->name ?? 'Unknown User';
                }

                return [
                    'id' => $message->id,
                    'content' => $message->content,
                    'created_at' => $message->created_at->format('M d, H:i A'),
                    'is_sender' => $message->sender_id === $user->id,
                    'sender_name' => $senderName,
                    'read_at' => $message->read_at ? $message->read_at->format('M d, H:i A') : null,
                ];
            }),
            'coordinator_avatar' => $coordinatorUser->profile_avatar_url ? asset('storage/' . $coordinatorUser->profile_avatar_url) : asset('images/general.png'),
            'coordinator_name' => $fullCoordinatorName, // This is for the header display
        ]);
    }

    /**
     * Send a reply to an existing conversation as a participant.
     */
    public function reply(Request $request, Conversation $conversation)
    {
        try {
            $request->validate([
                'content' => 'required|string',
            ]);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }

        $sender = Auth::user(); // Authenticated User (Participant)
        $participant = $sender->participant;

        // Ensure the conversation belongs to the authenticated participant
        if (!$participant || $conversation->participant_id !== $participant->id) {
            return response()->json(['message' => 'Unauthorized to reply to this conversation.'], 403);
        }

        // The receiver for a participant's reply in an 'sc_to_participant' conversation
        // is the support coordinator's user.
        $receiverUser = $conversation->supportCoordinator->user;

        if (!$receiverUser) {
            return response()->json(['message' => 'Could not determine recipient for this conversation type.'], 400);
        }

        $message = $conversation->messages()->create([
            'sender_id' => $sender->id, // The User ID of the Participant
            'receiver_id' => $receiverUser->id, // The User ID of the Support Coordinator
            'content' => $request->content,
            'type' => 'text',
            'original_sender_role' => 'participant',
            'original_recipient_role' => 'coordinator', // The role of the user receiving the message
        ]);

        // Update last_message_at on the conversation
        $conversation->update(['last_message_at' => now()]);

        // event(new MessageSent($message)); // Your MessageSent event expects a Message model

        return response()->json([
            'message' => 'Reply sent successfully!',
            'data' => [
                'id' => $message->id,
                'content' => $message->content,
                'created_at' => $message->created_at->format('M d, H:i A'),
                'is_sender' => true, // Sent by current user
                'sender_name' => 'You', // When you send, it's always "You"
                'read_at' => $message->read_at ? $message->read_at->format('M d, H:i A') : null,
            ]
        ], 200);
    }

    /**
     * Participants send messages to Support Coordinators.
     * This creates or appends to an 'sc_to_participant' conversation (from participant's perspective).
     */
    public function sendMessageToCoordinator(Request $request, $supportCoordinatorId)
    {
        try {
            $request->validate([
                'message_body' => 'required|string',
            ]);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }

        $sender = Auth::user(); // The authenticated user (who should be a Participant)

        if ($sender->role !== 'participant') {
            return response()->json(['message' => 'Unauthorized: Only participants can send messages via this endpoint.'], 403);
        }

        $participant = $sender->participant;

        if (!$participant) {
            return response()->json(['message' => 'Participant profile not found for the authenticated user.'], 403);
        }

        $supportCoordinator = SupportCoordinator::with('user')->find($supportCoordinatorId);

        if (!$supportCoordinator || !$supportCoordinator->user) {
            return response()->json(['message' => 'Support Coordinator or associated user not found.'], 404);
        }

        $receiverUser = $supportCoordinator->user; // The User model associated with the coordinator

        $conversation = Conversation::where('type', 'sc_to_participant')
            ->where('participant_id', $participant->id)
            ->where('support_coordinator_id', $supportCoordinator->id)
            ->first();

        if (!$conversation) {
            $conversation = Conversation::create([
                'type' => 'sc_to_participant',
                'participant_id' => $participant->id,
                'support_coordinator_id' => $supportCoordinator->id,
                'last_message_at' => now(),
            ]);
        } else {
            $conversation->update(['last_message_at' => now()]);
        }

        $message = $conversation->messages()->create([
            'sender_id' => $sender->id, // The User ID of the Participant
            'receiver_id' => $receiverUser->id, // The User ID of the Support Coordinator
            'content' => $request->message_body,
            'type' => 'text',
            'original_sender_role' => 'participant',
            'original_recipient_role' => 'coordinator',
        ]);

        // event(new MessageSent($message));

        return response()->json([
            'message' => 'Message sent successfully!',
            'data' => $message,
            'conversation' => $conversation
        ], 200);
    }
}