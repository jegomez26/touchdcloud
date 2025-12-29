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
use Illuminate\Support\Facades\Log;
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

        $conversations = Conversation::where(function($query) use ($participant) {
                $query->where('participant_id', $participant->id)
                      ->orWhere('sender_participant_id', $participant->id)
                      ->orWhere('matching_for_participant_id', $participant->id);
            })
            ->with(['supportCoordinator.user', 'provider', 'senderParticipant', 'participant', 'messages' => function($query) {
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
        if (!$participant || 
            ($conversation->participant_id !== $participant->id && 
             $conversation->sender_participant_id !== $participant->id &&
             $conversation->matching_for_participant_id !== $participant->id)) {
            abort(403, 'Unauthorized to view this conversation.');
        }

        // Load all messages for this conversation, ordered oldest to newest for chronological display
        // Eager load sender and receiver User models to get their names/roles
        $messages = $conversation->messages()->with('sender', 'receiver')->oldest()->get();

        // Mark messages sent to the current participant as read
        $conversation->messages()
                     ->where('receiver_id', $user->id)
                     ->whereNull('read_at')
                     ->update(['read_at' => now()]);

        // Load conversation with all necessary relationships
        $conversation->load(['supportCoordinator.user', 'provider', 'senderParticipant', 'participant']);

        // Get all conversations for the sidebar
        $conversations = Conversation::where(function($query) use ($participant) {
                $query->where('participant_id', $participant->id)
                      ->orWhere('sender_participant_id', $participant->id)
                      ->orWhere('matching_for_participant_id', $participant->id);
            })
            ->with(['supportCoordinator.user', 'provider', 'senderParticipant', 'participant', 'messages' => function($query) {
                $query->latest()->limit(1);
            }])
            ->orderByDesc('last_message_at')
            ->paginate(10);

        // Check if this is an API request (for the unified messaging component)
        if (request()->wantsJson() || request()->header('Accept') === 'application/json') {
            // Prepare response data based on conversation type
            $responseData = [
                'conversation' => [
                    'id' => $conversation->id,
                    'type' => $conversation->type,
                    'participant_name' => $conversation->participant ? trim($conversation->participant->first_name . ' ' . $conversation->participant->last_name) : '',
                    'participant_code' => $conversation->participant?->participant_code_name,
                    'participant_id' => $conversation->participant_id,
                    'sender_participant_code' => $conversation->senderParticipant?->participant_code_name,
                    'sender_participant_id' => $conversation->sender_participant_id,
                    'provider_code' => $conversation->provider?->provider_code_name,
                    'provider_id' => $conversation->provider_id,
                    'support_coordinator_code' => $conversation->supportCoordinator?->sup_coor_code_name,
                    'support_coordinator_id' => $conversation->support_coordinator_id,
                    'last_message_at' => $conversation->last_message_at?->toIso8601String(),
                ],
                'messages' => $messages->map(function($message) use ($user) {
                    $senderName = '';
                    if ($message->sender_id === $user->id) {
                        $senderName = 'You';
                    } else {
                        // Get sender name based on their role
                        if ($message->sender->role === 'coordinator') {
                            $senderName = $message->sender->supportCoordinator?->sup_coor_code_name ?? 'SC-' . $message->sender->id;
                        } elseif ($message->sender->role === 'provider') {
                            $senderName = $message->sender->provider?->provider_code_name ?? 'PR-' . $message->sender->id;
                        } elseif ($message->sender->role === 'participant') {
                            $senderName = $message->sender->participant?->participant_code_name ?? 'PA-' . $message->sender->id;
                        } else {
                            $senderName = 'Unknown User';
                        }
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
            ];

            return response()->json($responseData);
        }

        return view('indiv.messages.show', compact('conversation', 'messages', 'conversations'));
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

        // Load conversation with all necessary relationships
        $conversation->load(['supportCoordinator.user', 'provider.user', 'senderParticipant.user', 'participant.user']);

        // Ensure the conversation belongs to the authenticated participant
        if (!$participant || 
            ($conversation->participant_id !== $participant->id && 
             $conversation->sender_participant_id !== $participant->id &&
             $conversation->matching_for_participant_id !== $participant->id)) {
            return response()->json(['message' => 'Unauthorized to reply to this conversation.'], 403);
        }

        // Determine the receiver based on conversation type
        $receiverUser = null;
        
        if ($conversation->type === 'sc_to_participant') {
            // Participant replying to support coordinator
            if (!$conversation->supportCoordinator) {
                return response()->json(['message' => 'Support coordinator not found for this conversation.'], 400);
            }
            $receiverUser = $conversation->supportCoordinator->user;
        } elseif ($conversation->type === 'participant_to_participant') {
            // Participant replying to another participant
            if ($conversation->participant_id === $participant->id) {
                // Current participant is the main participant, reply goes to sender participant
                if (!$conversation->senderParticipant) {
                    return response()->json(['message' => 'Sender participant not found for this conversation.'], 400);
                }
                $receiverUser = $conversation->senderParticipant->user;
            } else {
                // Current participant is the sender participant, reply goes to main participant
                if (!$conversation->participant) {
                    return response()->json(['message' => 'Participant not found for this conversation.'], 400);
                }
                $receiverUser = $conversation->participant->user;
            }
        } elseif ($conversation->type === 'provider_to_participant') {
            // Participant replying to provider
            if (!$conversation->provider) {
                return response()->json(['message' => 'Provider not found for this conversation.'], 400);
            }
            $receiverUser = $conversation->provider->user;
        }

        if (!$receiverUser) {
            return response()->json(['message' => 'Could not determine recipient for this conversation type.'], 400);
        }

        // Determine recipient role based on conversation type
        $recipientRole = 'coordinator'; // default
        if ($conversation->type === 'participant_to_participant') {
            $recipientRole = 'participant';
        } elseif ($conversation->type === 'provider_to_participant') {
            $recipientRole = 'provider';
        }

        $message = $conversation->messages()->create([
            'sender_id' => $sender->id, // The User ID of the Participant
            'receiver_id' => $receiverUser->id, // The User ID of the recipient
            'content' => $request->content,
            'type' => 'text',
            'sent_at' => now(),
            'original_sender_role' => 'participant',
            'original_recipient_role' => $recipientRole,
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
            'sent_at' => now(),
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

    /**
     * Send a message to another participant (for matching purposes)
     */
    public function sendMessageToParticipant(Request $request, $targetParticipantId)
    {
        \Log::info('sendMessageToParticipant called', [
            'targetParticipantId' => $targetParticipantId,
            'request_content' => $request->all(),
            'user_id' => Auth::id()
        ]);

        try {
            $request->validate([
                'content' => 'required|string',
            ]);
        } catch (ValidationException $e) {
            \Log::error('Validation failed', ['errors' => $e->errors()]);
            return response()->json(['message' => 'Validation failed', 'errors' => $e->errors()], 422);
        }

        $sender = Auth::user();

        if ($sender->role !== 'participant') {
            \Log::error('Unauthorized user role', ['role' => $sender->role]);
            return response()->json(['message' => 'Unauthorized: Only participants can send messages via this endpoint.'], 403);
        }

        $senderParticipant = $sender->participant;

        if (!$senderParticipant) {
            \Log::error('No participant profile found', ['user_id' => $sender->id]);
            return response()->json(['message' => 'Participant profile not found for the authenticated user.'], 403);
        }

        $targetParticipant = Participant::with(['user', 'supportCoordinator.user', 'addedByUser.provider'])->find($targetParticipantId);

        \Log::info('Target participant lookup', [
            'targetParticipantId' => $targetParticipantId,
            'targetParticipant' => $targetParticipant ? [
                'id' => $targetParticipant->id,
                'user_id' => $targetParticipant->user_id,
                'participant_code_name' => $targetParticipant->participant_code_name,
                'has_direct_user' => $targetParticipant->user !== null,
                'has_sc' => $targetParticipant->supportCoordinator !== null,
                'added_by_user' => $targetParticipant->addedByUser !== null,
                'sc_user_email' => $targetParticipant->supportCoordinator && $targetParticipant->supportCoordinator->user ? $targetParticipant->supportCoordinator->user->email : null,
                'added_by_user_email' => $targetParticipant->addedByUser ? $targetParticipant->addedByUser->email : null
            ] : null
        ]);

        if (!$targetParticipant) {
            \Log::error('Target participant not found', [
                'targetParticipantId' => $targetParticipantId
            ]);
            return response()->json(['message' => 'Target participant not found.'], 404);
        }

        // Determine the receiver user based on participant type
        $receiverUser = null;
        $conversationType = null;
        
        \Log::info('Determining conversation type and receiver', [
            'targetParticipantId' => $targetParticipant->id,
            'has_direct_user' => $targetParticipant->user !== null,
            'has_sc' => $targetParticipant->supportCoordinator !== null,
            'has_added_by_user' => $targetParticipant->addedByUser !== null,
            'sc_id' => $targetParticipant->supportCoordinator ? $targetParticipant->supportCoordinator->id : null,
            'provider_id' => $targetParticipant->addedByUser && $targetParticipant->addedByUser->provider ? $targetParticipant->addedByUser->provider->id : null
        ]);
        
        if ($targetParticipant->user) {
            // Direct participant with user account
            $receiverUser = $targetParticipant->user;
            $conversationType = 'participant_to_participant';
        } elseif ($targetParticipant->supportCoordinator && $targetParticipant->supportCoordinator->user) {
            // Participant under support coordinator
            $receiverUser = $targetParticipant->supportCoordinator->user;
            $conversationType = 'sc_to_participant';
        } elseif ($targetParticipant->addedByUser) {
            // Participant added by a user (could be provider or other)
            $receiverUser = $targetParticipant->addedByUser;
            $conversationType = 'provider_to_participant';
        } else {
            \Log::error('No valid receiver found for participant', [
                'targetParticipantId' => $targetParticipantId,
                'has_direct_user' => $targetParticipant->user !== null,
                'has_sc' => $targetParticipant->supportCoordinator !== null,
                'added_by_user' => $targetParticipant->addedByUser !== null
            ]);
            return response()->json(['message' => 'No valid contact method found for this participant.'], 404);
        }

        \Log::info('Participants found', [
            'senderParticipantId' => $senderParticipant->id,
            'targetParticipantId' => $targetParticipant->id,
            'receiverUserId' => $receiverUser->id
        ]);

        // Find or create conversation based on participant type
        $conversation = $this->findOrCreateConversation($senderParticipant, $targetParticipant, $conversationType);

        \Log::info('Conversation handling', [
            'conversationType' => $conversationType,
            'conversationId' => $conversation ? $conversation->id : null,
            'isNew' => $conversation ? $conversation->wasRecentlyCreated : false
        ]);

        // Determine recipient role based on conversation type
        $recipientRole = 'participant'; // default
        if ($conversationType === 'sc_to_participant') {
            $recipientRole = 'coordinator';
        } elseif ($conversationType === 'provider_to_participant') {
            $recipientRole = 'provider';
        }

        try {
            $message = $conversation->messages()->create([
                'sender_id' => $sender->id,
                'receiver_id' => $receiverUser->id,
                'content' => $request->content,
                'type' => 'text',
                'sent_at' => now(),
                'original_sender_role' => 'participant',
                'original_recipient_role' => $recipientRole,
            ]);

            \Log::info('Message created successfully', [
                'message_id' => $message->id,
                'conversation_id' => $conversation->id,
                'sender_id' => $sender->id,
                'receiver_id' => $receiverUser->id
            ]);

            return response()->json([
                'message' => 'Message sent successfully!',
                'data' => [
                    'id' => $message->id,
                    'content' => $message->content,
                    'created_at' => $message->created_at->format('M d, H:i A'),
                    'is_sender' => true,
                    'sender_name' => 'You',
                    'read_at' => $message->read_at ? $message->read_at->format('M d, H:i A') : null,
                ],
                'conversation' => $conversation
            ], 200);
        } catch (\Exception $e) {
            \Log::error('Failed to create message', [
                'error' => $e->getMessage(),
                'conversation_id' => $conversation->id,
                'sender_id' => $sender->id,
                'receiver_id' => $receiverUser->id
            ]);

            return response()->json([
                'message' => 'Failed to send message: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Find or create conversation based on participant type
     * Uses participant_id and matching_for_participant_id to check for existing conversations
     */
    private function findOrCreateConversation($senderParticipant, $targetParticipant, $conversationType)
    {
        // First check if a conversation already exists between these two participants
        $existingConversation = Conversation::where(function($query) use ($senderParticipant, $targetParticipant) {
            // Check for conversations where sender is the main participant and target is the matching participant
            $query->where(function($q) use ($senderParticipant, $targetParticipant) {
                $q->where('participant_id', $senderParticipant->id)
                  ->where('matching_for_participant_id', $targetParticipant->id);
            })
            // Check for conversations where target is the main participant and sender is the matching participant
            ->orWhere(function($q) use ($senderParticipant, $targetParticipant) {
                $q->where('participant_id', $targetParticipant->id)
                  ->where('matching_for_participant_id', $senderParticipant->id);
            });
        })->first();

        if ($existingConversation) {
            $existingConversation->update(['last_message_at' => now()]);
            return $existingConversation;
        }

        // If no existing conversation, create a new one based on conversation type
        $conversationData = [
            'participant_id' => $targetParticipant->id,
            'matching_for_participant_id' => $senderParticipant->id,
            'last_message_at' => now(),
        ];

        if ($conversationType === 'sc_to_participant') {
            $conversationData['type'] = 'sc_to_participant';
            $conversationData['support_coordinator_id'] = $targetParticipant->supportCoordinator->id;
            \Log::info('Creating SC conversation', [
                'participant_id' => $targetParticipant->id,
                'matching_for_participant_id' => $senderParticipant->id,
                'support_coordinator_id' => $targetParticipant->supportCoordinator->id
            ]);
        } elseif ($conversationType === 'provider_to_participant') {
            $conversationData['type'] = 'provider_to_participant';
            $providerId = $targetParticipant->addedByUser->provider->id ?? null;
            $conversationData['provider_id'] = $providerId;
            \Log::info('Creating provider conversation', [
                'participant_id' => $targetParticipant->id,
                'matching_for_participant_id' => $senderParticipant->id,
                'provider_id' => $providerId,
                'added_by_user_id' => $targetParticipant->addedByUser->id
            ]);
        } else {
            $conversationData['type'] = 'participant_to_participant';
            \Log::info('Creating participant-to-participant conversation', [
                'participant_id' => $targetParticipant->id,
                'matching_for_participant_id' => $senderParticipant->id
            ]);
        }

        \Log::info('Final conversation data', $conversationData);
        return Conversation::create($conversationData);
    }
}