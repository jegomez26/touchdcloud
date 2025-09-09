<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;
use App\Models\Conversation;
use App\Models\Participant;
use App\Models\User;
use App\Models\SupportCoordinator; // Import the SupportCoordinator model
use Illuminate\Support\Facades\Auth;
use App\Events\MessageSent;
use Illuminate\Validation\ValidationException;

class CoordinatorMessageController extends Controller
{

    /**
     * Display a listing of conversations for the authenticated support coordinator.
     */
    public function index()
    {
        $user = Auth::user();
        $supportCoordinator = $user->supportCoordinator;

        if (!$supportCoordinator) {
            // Handle case where coordinator profile is missing (should be caught by middleware normally)
            abort(403, 'Support Coordinator profile not found.');
        }

        // Get conversations for this support coordinator, eager load related participant and last message
        $conversations = Conversation::forSupportCoordinator($supportCoordinator->id)
                                ->with(['participant.user', 'messages' => function($query) {
                                    $query->latest()->limit(1); // Get only the latest message
                                }])
                                ->orderByDesc('last_message_at') // Order by latest message
                                ->paginate(10); // Or whatever pagination you prefer

        $initialConversationId = $conversations->first() ? $conversations->first()->id : null;
                        

        return view('supcoor.messages.index', compact('conversations', 'initialConversationId'));
    }

    /**
     * Display a specific conversation.
     */
    public function show(Conversation $conversation)
    {
        $user = Auth::user();
        $supportCoordinator = $user->supportCoordinator;

        // Ensure the conversation belongs to the authenticated support coordinator
        if ($conversation->support_coordinator_id !== $supportCoordinator->id) {
            abort(403, 'Unauthorized to view this conversation.');
        }

        // Load all messages for this conversation, ordered oldest to newest for chronological display
        $messages = $conversation->messages()->with('sender', 'receiver')->oldest()->get();

        // Mark messages sent by the participant as read
        $conversation->messages()
                     ->where('receiver_id', $user->id) // Messages sent TO the current SC
                     ->whereNull('read_at')
                     ->update(['read_at' => now()]);

        return response()->json([
            'conversation' => $conversation->load('participant.user'), // Load participant for display
            'messages' => $messages->map(function($message) use ($user) {
                // Map to simplify data for frontend and add a 'is_sender' flag
                return [
                    'id' => $message->id,
                    'content' => $message->content,
                    'created_at' => $message->created_at->format('M d, H:i A'),
                    'is_sender' => $message->sender_id === $user->id,
                    'sender_name' => $message->sender_id === $user->id ? 'You' : ($message->sender->name ?? 'Participant'),
                    'read_at' => $message->read_at ? $message->read_at->format('M d, H:i A') : null,
                ];
            }),
            'participant_avatar' => $conversation->participant->profile_avatar_url ? asset('storage/' . $conversation->participant->profile_avatar_url) : ($conversation->participant->gender === 'Male' ? asset('images/male' . rand(1, 2) . '.png') : ($conversation->participant->gender === 'Female' ? asset('images/female' . rand(1, 2) . '.png') : asset('images/general.png'))),
            'participant_code_name' => $conversation->participant->participant_code_name ?? 'Unknown Participant',
        ]);
    }

    /**
     * Send a reply to an existing conversation.
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

        $sender = Auth::user();
        $supportCoordinator = $sender->supportCoordinator;

        // Ensure the conversation belongs to the authenticated support coordinator
        if ($conversation->support_coordinator_id !== $supportCoordinator->id) {
            return response()->json(['message' => 'Unauthorized to reply to this conversation.'], 403);
        }

        // Determine the receiver based on the conversation type and sender role
        $receiverUser = null;
        if ($conversation->participant_id && $conversation->participant && $conversation->participant->user) {
            $receiverUser = $conversation->participant->user;
        } elseif ($conversation->support_coordinator_id && $conversation->supportCoordinator) {
            $receiverUser = $conversation->supportCoordinator->user;
        } elseif ($conversation->provider_id && $conversation->provider) {
            $receiverUser = $conversation->provider->user;
        }

        if (!$receiverUser) {
            return response()->json(['message' => 'Could not determine recipient for this conversation type.'], 400);
        }

        $message = $conversation->messages()->create([
            'sender_id' => $sender->id,
            'receiver_id' => $receiverUser->id,
            'content' => $request->content,
            'type' => 'text',
            'original_sender_role' => 'coordinator', // The sender is always the coordinator here
            'original_recipient_role' => $receiverUser->role,
        ]);

        // Update last_message_at on the conversation
        $conversation->update(['last_message_at' => now()]);

        // broadcast(new MessageSent($message))->toOthers();

        return response()->json([
            'message' => 'Reply sent successfully!',
            'data' => [
                'id' => $message->id,
                'content' => $message->content,
                'created_at' => $message->created_at->format('M d, H:i A'),
                'is_sender' => true, // Sent by current user
                'sender_name' => 'You',
                'read_at' => $message->read_at ? $message->read_at->format('M d, H:i A') : null,
            ]
        ], 200);
    }

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
                // 'message_subject' => 'nullable|string|max:255',
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
                // 'subject' => $request->message_subject, // Ensure 'subject' column exists in 'conversations' table and is fillable
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

        // broadcast(new MessageSent($message))->toOthers();

        return response()->json([
            'message' => 'Message sent successfully!',
            'data' => $message,
            'conversation' => $conversation
        ], 200);
    }
}