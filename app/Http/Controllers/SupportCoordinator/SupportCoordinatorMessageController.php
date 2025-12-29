<?php

namespace App\Http\Controllers\SupportCoordinator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Participant;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use App\Notifications\MessageReceived;

class SupportCoordinatorMessageController extends Controller
{
    /**
     * Support Coordinator inbox - list conversations for this support coordinator
     */
    public function index()
    {
        $user = Auth::user();
        $supportCoordinator = $user->supportCoordinator;
        abort_unless($supportCoordinator, 403);

        $conversations = Conversation::where(function($query) use ($supportCoordinator, $user) {
                $query->where('support_coordinator_id', $supportCoordinator->id)
                      ->orWhere('initiator_user_id', $user->id)
                      ->orWhere('recipient_user_id', $user->id);
            })
            ->with(['participant', 'provider', 'initiatorParticipant', 'recipientParticipant', 'messages' => function ($q) {
                $q->latest()->limit(1);
            }])
            ->orderByDesc('last_message_at')
            ->paginate(12);

        $initialConversationId = $conversations->first() ? $conversations->first()->id : null;

        return view('supcoor.messages.sc-inbox', compact('conversations', 'initialConversationId'));
    }

    /**
     * Show a specific conversation
     */
    public function show(Request $request, Conversation $conversation)
    {
        $user = Auth::user();
        $supportCoordinator = $user->supportCoordinator;
        abort_unless($supportCoordinator && (
            $conversation->support_coordinator_id === $supportCoordinator->id || 
            $conversation->initiator_user_id === $user->id || 
            $conversation->recipient_user_id === $user->id
        ), 403);

        $messages = $conversation->messages()->with(['sender', 'receiver'])->oldest()->get();

        // mark messages to support coordinator as read
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
                    'initiator_participant_code' => optional($conversation->initiatorParticipant)->participant_code_name,
                    'initiator_participant_code_name' => optional($conversation->initiatorParticipant)->participant_code_name,
                    'recipient_participant_name' => optional($conversation->recipientParticipant)->first_name . ' ' . optional($conversation->recipientParticipant)->last_name,
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
        $conversations = Conversation::where(function($query) use ($supportCoordinator, $user) {
                $query->where('support_coordinator_id', $supportCoordinator->id)
                      ->orWhere('initiator_user_id', $user->id)
                      ->orWhere('recipient_user_id', $user->id);
            })
            ->with(['participant', 'provider', 'initiatorParticipant', 'recipientParticipant', 'messages' => function ($q) {
                $q->latest()->limit(1);
            }])
            ->orderByDesc('last_message_at')
            ->paginate(12);

        return view('supcoor.messages.sc-conversation', compact('conversation', 'messages', 'conversations'));
    }

    /**
     * Reply in a conversation
     */
    public function reply(Request $request, Conversation $conversation)
    {
        $request->validate(['content' => ['required', 'string', 'max:5000']]);

        $user = Auth::user();
        $supportCoordinator = $user->supportCoordinator;
        abort_unless($supportCoordinator && (
            $conversation->support_coordinator_id === $supportCoordinator->id || 
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
        if (!$receiverUser && $conversation->provider_id) {
            $receiverUser = optional($conversation->provider)->user;
        }
        if (!$receiverUser && $conversation->participant_id) {
            $receiverUser = optional($conversation->participant)->user;
        }
        if (!$receiverUser && $conversation->support_coordinator_id && $conversation->support_coordinator_id !== $supportCoordinator->id) {
            $receiverUser = optional($conversation->supportCoordinator)->user;
        }

        // Fallback to last message sender if not support coordinator
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

        return redirect()->route('sc.messages.show', $conversation)->with('success', 'Message sent');
    }
}
