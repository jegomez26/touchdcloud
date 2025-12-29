<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Participant;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use App\Notifications\MessageReceived;

class ProviderMessageController extends Controller
{
    /**
     * Provider inbox - list conversations for this provider
     */
    public function index()
    {
        $user = Auth::user();
        $provider = $user->provider;
        abort_unless($provider, 403);

        // Get subscription status
        $subscriptionStatus = \App\Services\SubscriptionService::getSubscriptionStatus();

        $conversations = Conversation::where(function($query) use ($provider, $user) {
                $query->where('provider_id', $provider->id)
                      ->orWhere('initiator_user_id', $user->id)
                      ->orWhere('recipient_user_id', $user->id);
            })
            ->with(['participant', 'supportCoordinator', 'initiatorParticipant', 'recipientParticipant', 'messages' => function ($q) {
                $q->latest()->limit(1);
            }])
            ->orderByDesc('last_message_at')
            ->paginate(12);

        $initialConversationId = $conversations->first() ? $conversations->first()->id : null;

        return view('company.messages.provider-inbox', compact('conversations', 'subscriptionStatus', 'initialConversationId'));
    }

    /**
     * Show a specific conversation
     */
    public function show(Request $request, Conversation $conversation)
    {
        $user = Auth::user();
        $provider = $user->provider;
        abort_unless($provider && (
            $conversation->provider_id === $provider->id || 
            $conversation->initiator_user_id === $user->id || 
            $conversation->recipient_user_id === $user->id
        ), 403);

        $messages = $conversation->messages()->with(['sender', 'receiver'])->oldest()->get();

        // mark messages to provider as read
        $conversation->messages()
            ->where('receiver_id', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        if ($request->wantsJson()) {
            // Determine header display rules
            $firstMsg = $conversation->messages()->oldest()->first();
            $isFirstReceiver = $firstMsg && $firstMsg->receiver_id === $user->id;
            $participantName = trim(($conversation->participant->first_name ?? '') . ' ' . ($conversation->participant->last_name ?? ''));
            $fromCode = null;
            if ($firstMsg) {
                if ($firstMsg->original_sender_role === 'coordinator') {
                    $fromCode = optional($conversation->supportCoordinator)->sup_coor_code_name;
                } elseif ($firstMsg->original_sender_role === 'provider') {
                    $fromCode = optional($conversation->provider)->provider_code_name;
                } else {
                    $fromCode = optional($conversation->participant)->participant_code_name;
                }
            }

            // Get matching participant info
            $matchingParticipantName = '';
            if ($conversation->matchingForParticipant) {
                $matchingParticipantName = trim(($conversation->matchingForParticipant->first_name ?? '') . ' ' . ($conversation->matchingForParticipant->last_name ?? ''));
            }
            
            return response()->json([
                'conversation' => [
                    'id' => $conversation->id,
                    'type' => $conversation->type,
                    'participant_name' => $participantName,
                    'participant_code' => optional($conversation->participant)->participant_code_name,
                    'participant_id' => $conversation->participant_id,
                    'matching_for_participant_name' => $matchingParticipantName,
                    'matching_for_participant_id' => $conversation->matching_for_participant_id,
                    'support_coordinator_code' => optional($conversation->supportCoordinator)->sup_coor_code_name,
                    'support_coordinator_id' => $conversation->support_coordinator_id,
                    'coordinator_code_name' => optional($conversation->supportCoordinator)->sup_coor_code_name,
                    'provider_code' => optional($conversation->provider)->provider_code_name,
                    'provider_id' => $conversation->provider_id,
                    'provider_code_name' => optional($conversation->provider)->provider_code_name,
                    'from_code' => $fromCode,
                    'last_message_at' => optional($conversation->last_message_at)?->toIso8601String(),
                    // New fields for improved context
                    'initiator_user_id' => $conversation->initiator_user_id,
                    'recipient_user_id' => $conversation->recipient_user_id,
                    'initiator_participant_id' => $conversation->initiator_participant_id,
                    'recipient_participant_id' => $conversation->recipient_participant_id,
                    'initiator_participant_name' => optional($conversation->initiatorParticipant)->first_name . ' ' . optional($conversation->initiatorParticipant)->last_name,
                    'recipient_participant_code' => optional($conversation->recipientParticipant)->participant_code_name,
                    'recipient_participant_code_name' => optional($conversation->recipientParticipant)->participant_code_name,
                ],
                'messages' => $messages->map(function ($m) use ($user) {
                    return [
                        'id' => $m->id,
                        'content' => $m->content,
                        'created_at' => $m->created_at->toIso8601String(),
                        'created_human' => $m->created_at->format('M d, Y H:i'),
                        'is_sender' => $m->sender_id === $user->id,
                        'read_at' => $m->read_at ? $m->read_at->toIso8601String() : null,
                    ];
                }),
            ]);
        }

        // Get all conversations for the sidebar
        $conversations = Conversation::where(function($query) use ($provider, $user) {
                $query->where('provider_id', $provider->id)
                      ->orWhere('initiator_user_id', $user->id)
                      ->orWhere('recipient_user_id', $user->id);
            })
            ->with(['participant', 'supportCoordinator', 'initiatorParticipant', 'recipientParticipant', 'messages' => function ($q) {
                $q->latest()->limit(1);
            }])
            ->orderByDesc('last_message_at')
            ->paginate(12);

        $subscriptionStatus = \App\Services\SubscriptionService::getSubscriptionStatus();

        return view('company.messages.provider-conversation', compact('conversation', 'messages', 'conversations', 'subscriptionStatus'));
    }

    /**
     * Reply in a conversation
     */
    public function reply(Request $request, Conversation $conversation)
    {
        $request->validate(['content' => ['required', 'string', 'max:5000']]);

        $user = Auth::user();
        $provider = $user->provider;
        abort_unless($provider && (
            $conversation->provider_id === $provider->id || 
            $conversation->initiator_user_id === $user->id || 
            $conversation->recipient_user_id === $user->id
        ), 403);

        // Determine receiver: use initiator/recipient user IDs from match request conversations, otherwise use conversation relationships
        $receiverUser = null;
        
        // First, check if conversation has initiator/recipient user IDs (from match requests)
        if ($conversation->initiator_user_id && $conversation->recipient_user_id) {
            if ($conversation->initiator_user_id === $user->id) {
                $receiverUser = User::find($conversation->recipient_user_id);
            } else {
                $receiverUser = User::find($conversation->initiator_user_id);
            }
        }
        
        // Fallback to conversation relationships
        if (!$receiverUser && $conversation->support_coordinator_id) {
            $receiverUser = optional($conversation->supportCoordinator)->user;
        }
        if (!$receiverUser && $conversation->participant_id) {
            $receiverUser = optional($conversation->participant)->user;
        }
        if (!$receiverUser && $conversation->provider_id && $conversation->provider_id !== $provider->id) {
            $receiverUser = optional($conversation->provider)->user;
        }

        // Fallback to last message sender if not provider
        if (!$receiverUser) {
            $last = $conversation->messages()->latest()->first();
            if ($last && $last->sender_id !== $user->id) {
                $receiverUser = $last->sender;
            }
        }

        abort_unless($receiverUser, 400, 'Recipient not found');

        $message = $conversation->messages()->create([
            'sender_id' => $user->id,
            'receiver_id' => $receiverUser->id,
            'content' => $request->input('content'),
            'type' => 'text',
            'original_sender_role' => $user->role,
            'original_recipient_role' => $receiverUser->role,
        ]);

        $conversation->update(['last_message_at' => now()]);

        $receiverUser->notify(new MessageReceived($message));

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Message sent',
                'data' => [
                    'id' => $message->id,
                    'content' => $message->content,
                    'created_at' => $message->created_at->toIso8601String(),
                    'is_sender' => true,
                ],
            ]);
        }

        return redirect()->route('provider.messages.show', $conversation)->with('success', 'Message sent');
    }
    /**
     * Send a message from the authenticated provider to the owner of the participant (SC or Provider).
     * If neither SC nor owner is found, and the participant has a linked user, send directly to participant.
     * Privacy: The response payload must not reveal participant name to the sender.
     */
    public function sendToOwner(Request $request, Participant $participant)
    {
        $request->validate([
            'content' => ['required', 'string', 'max:5000'],
        ]);

        $senderUser = Auth::user();

        if ($senderUser->role !== 'provider') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $provider = $senderUser->provider;
        if (!$provider) {
            return response()->json(['message' => 'Provider profile not found'], 403);
        }

        // Determine receiver priority: Support Coordinator user -> added_by_user -> direct participant user
        $receiverUser = null;

        if (!empty($participant->support_coordinator_id)) {
            $receiverUser = $participant->supportCoordinator->user;
        }

        if (!$receiverUser && !empty($participant->added_by_user_id)) {
            $receiverUser = User::find($participant->added_by_user_id);
        }

        if (!$receiverUser && !empty($participant->user_id)) {
            $receiverUser = User::find($participant->user_id);
        }

        if (!$receiverUser) {
            return response()->json(['message' => 'No recipient found for this participant'], 404);
        }

        // Determine conversation type
        $conversationType = 'provider_to_' . ($receiverUser->role === 'coordinator' ? 'sc' : ($receiverUser->role === 'provider' ? 'provider' : 'participant'));

        // Determine which participant the provider is looking for a match for
        $matchingForParticipantId = null;
        if ($conversationType === 'provider_to_sc' || $conversationType === 'provider_to_participant') {
            // Use selected_participant_id if provided (from matching context), otherwise find the first participant that belongs to this provider
            if ($request->has('selected_participant_id')) {
                $matchingForParticipantId = $request->input('selected_participant_id');
            } else {
                $providerParticipant = Participant::where('added_by_user_id', $senderUser->id)->first();
                if ($providerParticipant) {
                    $matchingForParticipantId = $providerParticipant->id;
                }
            }
        }

        // Find or create conversation - check for specific combination including initiator/recipient fields
        $conversationQuery = Conversation::where('type', $conversationType)
            ->where('participant_id', $participant->id)
            ->where('provider_id', $provider->id)
            ->where('matching_for_participant_id', $matchingForParticipantId)
            ->where('initiator_user_id', $senderUser->id)
            ->where('recipient_user_id', $receiverUser->id);

        if ($receiverUser->role === 'coordinator') {
            $conversationQuery->where('support_coordinator_id', $receiverUser->supportCoordinator?->id);
        }

        $conversation = $conversationQuery->first();

        if (!$conversation) {
            // Create new conversation with the specific matching_for_participant_id
            $conversation = Conversation::create([
                'type' => $conversationType,
                'support_coordinator_id' => $receiverUser->role === 'coordinator' ? ($receiverUser->supportCoordinator?->id) : null,
                'participant_id' => $participant->id,
                'matching_for_participant_id' => $matchingForParticipantId,
                'provider_id' => $provider->id,
                'initiator_user_id' => $senderUser->id, // Provider who initiated
                'recipient_user_id' => $receiverUser->id, // Support coordinator or provider who receives
                'initiator_participant_id' => $matchingForParticipantId, // Provider's participant being matched
                'recipient_participant_id' => $participant->id, // Matched participant
                'last_message_at' => now(),
            ]);
        } else {
            $conversation->update(['last_message_at' => now()]);
        }

        // Create message
        $message = $conversation->messages()->create([
            'sender_id' => $senderUser->id,
            'receiver_id' => $receiverUser->id,
            'content' => $request->input('content'),
            'type' => 'text',
            'original_sender_role' => $senderUser->role,
            'original_recipient_role' => $receiverUser->role,
        ]);

        // Notify receiver
        $receiverUser->notify(new MessageReceived($message));

        // Build response masking participant name for sender
        return response()->json([
            'message' => 'Message sent',
            'conversation_id' => $conversation->id,
            'participant' => [
                'id' => $participant->id,
                'participant_code_name' => $participant->participant_code_name,
            ],
        ], 200);
    }
}


