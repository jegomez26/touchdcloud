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

        $conversations = Conversation::where('provider_id', $provider->id)
            ->with(['participant', 'supportCoordinator', 'messages' => function ($q) {
                $q->latest()->limit(1);
            }])
            ->orderByDesc('last_message_at')
            ->paginate(12);

        return view('company.messages.provider-inbox', compact('conversations'));
    }

    /**
     * Show a specific conversation
     */
    public function show(Request $request, Conversation $conversation)
    {
        $user = Auth::user();
        $provider = $user->provider;
        abort_unless($provider && $conversation->provider_id === $provider->id, 403);

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

            return response()->json([
                'conversation' => [
                    'id' => $conversation->id,
                    'participant_name' => $isFirstReceiver ? ($participantName ?: null) : null,
                    'participant_code' => optional($conversation->participant)->participant_code_name,
                    'from_code' => $fromCode,
                    'last_message_at' => optional($conversation->last_message_at)?->toIso8601String(),
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

        return view('company.messages.provider-conversation', compact('conversation', 'messages'));
    }

    /**
     * Reply in a conversation
     */
    public function reply(Request $request, Conversation $conversation)
    {
        $request->validate(['content' => ['required', 'string', 'max:5000']]);

        $user = Auth::user();
        $provider = $user->provider;
        abort_unless($provider && $conversation->provider_id === $provider->id, 403);

        // determine receiver: if conversation has support_coordinator_id, receiver is that coordinator's user; else participant user or the user that last sent not provider
        $receiverUser = null;
        if ($conversation->support_coordinator_id) {
            $receiverUser = optional($conversation->supportCoordinator)->user;
        }
        if (!$receiverUser && $conversation->participant_id) {
            $receiverUser = optional($conversation->participant)->user;
        }

        // fallback to last message sender if not provider
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
            $receiverUser = User::find($participant->support_coordinator_id);
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

        // Find or create conversation
        $conversationQuery = Conversation::where('type', $conversationType)
            ->where('participant_id', $participant->id)
            ->where('provider_id', $provider->id);

        if ($receiverUser->role === 'coordinator') {
            $conversationQuery->where('support_coordinator_id', $receiverUser->supportCoordinator?->id);
        }

        $conversation = $conversationQuery->first();

        if (!$conversation) {
            $conversation = Conversation::create([
                'type' => $conversationType,
                'support_coordinator_id' => $receiverUser->role === 'coordinator' ? ($receiverUser->supportCoordinator?->id) : null,
                'participant_id' => $participant->id,
                'provider_id' => $provider->id,
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


