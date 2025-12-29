<?php

namespace App\Http\Controllers;

use App\Models\MatchRequest;
use App\Models\User;
use App\Models\Participant;
use App\Models\Conversation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Notifications\MatchRequestReceived;
use App\Notifications\MatchRequestAccepted;
use App\Notifications\MatchRequestRejected;

class MatchRequestController extends Controller
{
    /**
     * Send a match request by specifying a participant_id to infer the receiver.
     * This avoids requiring the client to know the receiver's user_id.
     */
    public function sendRequestForParticipant(Request $request)
    {
        $request->validate([
            'participant_id' => 'required|exists:participants,id',
            'sender_participant_id' => 'nullable|exists:participants,id',
            'message' => 'nullable|string|max:500',
        ]);

        $sender = Auth::user();
        $targetParticipant = Participant::findOrFail($request->participant_id);

        // Determine the sender participant ID
        $senderParticipantId = null;
        if ($request->has('sender_participant_id') && $request->sender_participant_id) {
            // Use provided sender participant ID
            $senderParticipantId = $request->sender_participant_id;
            
            // Verify the sender has access to this participant (for support coordinators/providers)
            if (in_array($sender->role, ['coordinator', 'provider'])) {
                $senderParticipant = Participant::findOrFail($senderParticipantId);
                if ($senderParticipant->added_by_user_id !== $sender->id) {
                    return response()->json(['error' => 'You do not have access to this participant'], 403);
                }
            }
        } else {
            // Auto-determine sender participant ID
            if ($sender->role === 'participant') {
                // If sender is a participant, use their own participant ID
                $senderParticipantId = optional($sender->participant)->id;
            } elseif (in_array($sender->role, ['coordinator', 'provider'])) {
                // For support coordinators/providers, find a participant they manage
                // Try to find a participant that belongs to the sender
                $managedParticipant = Participant::where('added_by_user_id', $sender->id)->first();
                if ($managedParticipant) {
                    $senderParticipantId = $managedParticipant->id;
                }
            }
        }

        // Determine the receiver user based on who owns the participant
        // Priority: added_by_user_id (providers/SCs), else participant->user (self/rep), else support_coordinator
        $receiver = null;
        if ($targetParticipant->added_by_user_id) {
            $receiver = User::find($targetParticipant->added_by_user_id);
        }
        if (!$receiver && $targetParticipant->user_id) {
            $receiver = User::find($targetParticipant->user_id);
        }
        if (!$receiver && $targetParticipant->support_coordinator_id) {
            $receiver = optional($targetParticipant->supportCoordinator)->user;
        }

        if (!$receiver) {
            return response()->json(['error' => 'Could not resolve recipient for this participant'], 400);
        }

        // Block admin targets; admins aren't match recipients
        if (in_array($receiver->role, ['admin','super_admin'])) {
            return response()->json(['error' => 'Cannot send match request to admin users'], 403);
        }

        // Admin/super_admin senders don't need requests; but if they try, return a helpful error
        if (in_array($sender->role, ['admin','super_admin'])) {
            return response()->json(['error' => 'Admins can start conversations directly without match requests'], 403);
        }

        // Prevent duplicate pending/accepted - check by participant IDs if available
        // Only use participant-based check to allow multiple participants from same provider/SC
        $existingRequest = null;
        
        if ($senderParticipantId && $targetParticipant->id) {
            // Check for duplicate based on participant IDs (allows multiple participants from same provider/SC)
            $existingRequest = MatchRequest::where(function($q) use ($senderParticipantId, $targetParticipant) {
                    $q->where('sender_participant_id', $senderParticipantId)
                      ->where('receiver_participant_id', $targetParticipant->id);
                })->orWhere(function($q) use ($senderParticipantId, $targetParticipant) {
                    $q->where('sender_participant_id', $targetParticipant->id)
                      ->where('receiver_participant_id', $senderParticipantId);
                })
                ->whereIn('status', ['pending','accepted'])
                ->first();
        }
        
        // Only fallback to user-based check if participant IDs are NOT available
        // This prevents false positives when same users have multiple participants
        if (!$existingRequest && (!$senderParticipantId || !$targetParticipant->id)) {
            $existingRequest = MatchRequest::where(function($q) use ($sender, $receiver) {
                    $q->where('sender_user_id', $sender->id)
                      ->where('receiver_user_id', $receiver->id);
                })->orWhere(function($q) use ($sender, $receiver) {
                    $q->where('sender_user_id', $receiver->id)
                      ->where('receiver_user_id', $sender->id);
                })
                ->whereIn('status', ['pending','accepted'])
                ->first();
        }

        if ($existingRequest) {
            return response()->json([
                'error' => $existingRequest->status === 'pending'
                    ? 'A match request is already pending between these participants'
                    : 'These participants are already matched'
            ], 400);
        }

        $matchRequest = MatchRequest::create([
            'sender_user_id' => $sender->id,
            'receiver_user_id' => $receiver->id,
            'sender_participant_id' => $senderParticipantId,
            'receiver_participant_id' => $targetParticipant->id,
            'message' => $request->message,
            'status' => 'pending',
        ]);

        $receiver->notify(new MatchRequestReceived($matchRequest));

        return response()->json([
            'success' => true,
            'message' => 'Match request sent successfully',
            'request' => $matchRequest,
        ]);
    }
    /**
     * Send a match request to another user
     */
    public function sendRequest(Request $request)
    {
        $request->validate([
            'receiver_user_id' => 'required|exists:users,id',
            'message' => 'nullable|string|max:500',
        ]);

        $sender = Auth::user();
        $receiver = User::findOrFail($request->receiver_user_id);

        // Check if sender is admin - admins can bypass the request system
        if ($sender->role === 'admin' || $sender->role === 'super_admin') {
            return response()->json([
                'error' => 'Admins can start conversations directly without match requests'
            ], 403);
        }

        // Check if receiver is admin - admins can't receive match requests
        if ($receiver->role === 'admin' || $receiver->role === 'super_admin') {
            return response()->json([
                'error' => 'Cannot send match request to admin users'
            ], 403);
        }

        // Check if there's already a pending or accepted request between these users
        $existingRequest = MatchRequest::where(function($query) use ($sender, $receiver) {
            $query->where('sender_user_id', $sender->id)
                  ->where('receiver_user_id', $receiver->id);
        })->orWhere(function($query) use ($sender, $receiver) {
            $query->where('sender_user_id', $receiver->id)
                  ->where('receiver_user_id', $sender->id);
        })->whereIn('status', ['pending', 'accepted'])->first();

        if ($existingRequest) {
            if ($existingRequest->status === 'pending') {
                return response()->json([
                    'error' => 'A match request is already pending between you and this user'
                ], 400);
            } elseif ($existingRequest->status === 'accepted') {
                return response()->json([
                    'error' => 'You are already matched with this user'
                ], 400);
            }
        }

        // Get participant profiles if they exist
        $senderParticipant = $sender->participant;
        $receiverParticipant = $receiver->participant;

        try {
            $matchRequest = MatchRequest::create([
                'sender_user_id' => $sender->id,
                'receiver_user_id' => $receiver->id,
                'sender_participant_id' => $senderParticipant?->id,
                'receiver_participant_id' => $receiverParticipant?->id,
                'message' => $request->message,
                'status' => 'pending',
            ]);

            // Send notification to receiver
            $receiver->notify(new MatchRequestReceived($matchRequest));

            return response()->json([
                'success' => true,
                'message' => 'Match request sent successfully',
                'request' => $matchRequest
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to send match request: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Accept a match request
     */
    public function acceptRequest(Request $request, $requestId)
    {
        $matchRequest = MatchRequest::findOrFail($requestId);
        $user = Auth::user();

        // Check if the current user is the receiver of this request
        if ($matchRequest->receiver_user_id !== $user->id) {
            return response()->json([
                'error' => 'You are not authorized to respond to this request'
            ], 403);
        }

        // Check if request is still pending
        if ($matchRequest->status !== 'pending') {
            return response()->json([
                'error' => 'This request has already been responded to'
            ], 400);
        }

        try {
            DB::beginTransaction();

            // Update the match request status
            $matchRequest->update([
                'status' => 'accepted',
                'responded_at' => now(),
            ]);

            // Create a conversation between the users
            $conversation = $this->createConversationFromMatchRequest($matchRequest);

            DB::commit();

            // Send notification to sender
            $matchRequest->senderUser->notify(new MatchRequestAccepted($matchRequest));

            return response()->json([
                'success' => true,
                'message' => 'Match request accepted successfully',
                'conversation_id' => $conversation->id
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Failed to accept match request: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reject a match request
     */
    public function rejectRequest(Request $request, $requestId)
    {
        $matchRequest = MatchRequest::findOrFail($requestId);
        $user = Auth::user();

        // Check if the current user is the receiver of this request
        if ($matchRequest->receiver_user_id !== $user->id) {
            return response()->json([
                'error' => 'You are not authorized to respond to this request'
            ], 403);
        }

        // Check if request is still pending
        if ($matchRequest->status !== 'pending') {
            return response()->json([
                'error' => 'This request has already been responded to'
            ], 400);
        }

        try {
            $matchRequest->update([
                'status' => 'rejected',
                'responded_at' => now(),
            ]);

            // Send notification to sender
            $matchRequest->senderUser->notify(new MatchRequestRejected($matchRequest));

            return response()->json([
                'success' => true,
                'message' => 'Match request rejected'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to reject match request: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get pending match requests for the current user
     */
    public function getPendingRequests()
    {
        $user = Auth::user();
        
        $pendingRequests = MatchRequest::pendingForUser($user->id)
            ->with(['senderUser', 'senderParticipant'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'requests' => $pendingRequests
        ]);
    }

    /**
     * Get sent match requests by the current user
     */
    public function getSentRequests()
    {
        $user = Auth::user();
        
        $sentRequests = MatchRequest::sentByUser($user->id)
            ->with(['receiverUser', 'receiverParticipant'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'requests' => $sentRequests
        ]);
    }

    /**
     * Check match request status between two participants
     */
    public function checkMatchRequestStatus(Request $request)
    {
        $request->validate([
            'receiver_participant_id' => 'required|exists:participants,id',
            'sender_participant_id' => 'nullable|exists:participants,id',
        ]);

        $user = Auth::user();
        $receiverParticipantId = $request->receiver_participant_id;
        
        // Auto-determine sender participant ID if not provided
        $senderParticipantId = $request->sender_participant_id;
        if (!$senderParticipantId) {
            if ($user->role === 'participant') {
                // If sender is a participant, use their own participant ID
                $senderParticipantId = optional($user->participant)->id;
            } elseif (in_array($user->role, ['coordinator', 'provider'])) {
                // For support coordinators/providers, use the provided sender_participant_id or find one they manage
                // This should be provided from the frontend (matchingContextParticipantId)
                $managedParticipant = Participant::where('added_by_user_id', $user->id)->first();
                if ($managedParticipant) {
                    $senderParticipantId = $managedParticipant->id;
                }
            }
        }
        
        if (!$senderParticipantId) {
            return response()->json([
                'success' => false,
                'error' => 'Could not determine sender participant ID'
            ], 400);
        }

        // Check for existing match request in either direction
        $existingRequest = MatchRequest::where(function($q) use ($senderParticipantId, $receiverParticipantId) {
                $q->where('sender_participant_id', $senderParticipantId)
                  ->where('receiver_participant_id', $receiverParticipantId);
            })->orWhere(function($q) use ($senderParticipantId, $receiverParticipantId) {
                $q->where('sender_participant_id', $receiverParticipantId)
                  ->where('receiver_participant_id', $senderParticipantId);
            })
            ->whereIn('status', ['pending', 'accepted'])
            ->with(['senderParticipant', 'receiverParticipant'])
            ->first();

        if ($existingRequest) {
            // Check if there's a conversation for accepted requests
            $conversationId = null;
            if ($existingRequest->status === 'accepted') {
                $conversation = Conversation::where(function($q) use ($existingRequest) {
                    $q->where('sender_participant_id', $existingRequest->sender_participant_id)
                      ->where('recipient_participant_id', $existingRequest->receiver_participant_id);
                })->orWhere(function($q) use ($existingRequest) {
                    $q->where('sender_participant_id', $existingRequest->receiver_participant_id)
                      ->where('recipient_participant_id', $existingRequest->sender_participant_id);
                })->first();
                
                if ($conversation) {
                    $conversationId = $conversation->id;
                }
            }

            return response()->json([
                'success' => true,
                'exists' => true,
                'status' => $existingRequest->status,
                'request_id' => $existingRequest->id,
                'conversation_id' => $conversationId,
                'message' => $existingRequest->message,
                'created_at' => $existingRequest->created_at,
            ]);
        }

        return response()->json([
            'success' => true,
            'exists' => false,
        ]);
    }

    /**
     * Check if users can start a conversation directly (for admins or already matched users)
     */
    public function canStartConversation(Request $request)
    {
        $request->validate([
            'receiver_user_id' => 'required|exists:users,id',
        ]);

        $sender = Auth::user();
        $receiver = User::findOrFail($request->receiver_user_id);

        // Admins can always start conversations
        if ($sender->role === 'admin' || $sender->role === 'super_admin') {
            return response()->json([
                'can_start' => true,
                'reason' => 'admin'
            ]);
        }

        // Check if there's an accepted match request
        $acceptedRequest = MatchRequest::where(function($query) use ($sender, $receiver) {
            $query->where('sender_user_id', $sender->id)
                  ->where('receiver_user_id', $receiver->id);
        })->orWhere(function($query) use ($sender, $receiver) {
            $query->where('sender_user_id', $receiver->id)
                  ->where('receiver_user_id', $sender->id);
        })->where('status', 'accepted')->first();

        if ($acceptedRequest) {
            return response()->json([
                'can_start' => true,
                'reason' => 'matched',
                'match_request_id' => $acceptedRequest->id
            ]);
        }

        // Check if there's a pending request
        $pendingRequest = MatchRequest::where('sender_user_id', $sender->id)
            ->where('receiver_user_id', $receiver->id)
            ->where('status', 'pending')
            ->first();

        if ($pendingRequest) {
            return response()->json([
                'can_start' => false,
                'reason' => 'pending_request',
                'match_request_id' => $pendingRequest->id
            ]);
        }

        return response()->json([
            'can_start' => false,
            'reason' => 'no_match'
        ]);
    }

    /**
     * Create a conversation from an accepted match request
     */
    private function createConversationFromMatchRequest(MatchRequest $matchRequest)
    {
        $sender = $matchRequest->senderUser;
        $receiver = $matchRequest->receiverUser;

        // Determine conversation type based on user roles
        $conversationType = $this->determineConversationType($sender, $receiver);

        $conversationData = [
            'type' => $conversationType,
            'last_message_at' => now(),
            'initiator_user_id' => $sender->id,
            'recipient_user_id' => $receiver->id,
        ];

        // Set participant IDs from match request if available
        if ($matchRequest->sender_participant_id) {
            $conversationData['sender_participant_id'] = $matchRequest->sender_participant_id;
            $conversationData['initiator_participant_id'] = $matchRequest->sender_participant_id;
        }
        
        if ($matchRequest->receiver_participant_id) {
            $conversationData['recipient_participant_id'] = $matchRequest->receiver_participant_id;
        }

        // Set appropriate IDs based on conversation type
        if ($conversationType === 'participant_to_participant') {
            $conversationData['participant_id'] = $matchRequest->receiver_participant_id;
            $conversationData['matching_for_participant_id'] = $matchRequest->sender_participant_id;
        } elseif ($conversationType === 'sc_to_participant') {
            // Support coordinator to participant OR participant to support coordinator
            if ($sender->role === 'coordinator') {
                // SC is sending to participant
                $conversationData['support_coordinator_id'] = $sender->supportCoordinator->id;
                $conversationData['participant_id'] = $matchRequest->receiver_participant_id;
                // Set matching_for_participant_id if sender has a participant they're matching for
                if ($matchRequest->sender_participant_id) {
                    $conversationData['matching_for_participant_id'] = $matchRequest->sender_participant_id;
                }
            } else {
                // Participant is sending to SC
                $conversationData['support_coordinator_id'] = $receiver->supportCoordinator->id;
                $conversationData['participant_id'] = $matchRequest->sender_participant_id;
            }
        } elseif ($conversationType === 'provider_to_participant') {
            // Provider to participant OR participant to provider
            if ($sender->role === 'provider') {
                // Provider is sending to participant
                $conversationData['provider_id'] = $sender->provider->id;
                $conversationData['participant_id'] = $matchRequest->receiver_participant_id;
                // Set matching_for_participant_id if sender has a participant they're matching for
                if ($matchRequest->sender_participant_id) {
                    $conversationData['matching_for_participant_id'] = $matchRequest->sender_participant_id;
                }
            } else {
                // Participant is sending to provider
                $conversationData['provider_id'] = $receiver->provider->id;
                $conversationData['participant_id'] = $matchRequest->sender_participant_id;
            }
        }

        return Conversation::create($conversationData);
    }

    /**
     * Determine conversation type based on user roles
     */
    private function determineConversationType(User $sender, User $receiver)
    {
        if ($sender->role === 'participant' && $receiver->role === 'participant') {
            return 'participant_to_participant';
        } elseif ($sender->role === 'coordinator' && $receiver->role === 'participant') {
            return 'sc_to_participant';
        } elseif ($sender->role === 'provider' && $receiver->role === 'participant') {
            return 'provider_to_participant';
        } elseif ($sender->role === 'participant' && $receiver->role === 'coordinator') {
            return 'sc_to_participant';
        } elseif ($sender->role === 'participant' && $receiver->role === 'provider') {
            return 'provider_to_participant';
        }

        // Default fallback
        return 'participant_to_participant';
    }
}