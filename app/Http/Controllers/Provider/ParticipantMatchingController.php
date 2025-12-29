<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Participant;
use App\Models\ParticipantMatch;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use App\Models\MatchRequest;
use App\Services\ParticipantMatchingService;
use App\Services\SubscriptionService;

class ParticipantMatchingController extends Controller
{
    protected $matchingService;

    public function __construct(ParticipantMatchingService $matchingService)
    {
        $this->matchingService = $matchingService;
    }

    /**
     * Display all participants added by the provider
     */
    public function index()
    {
        $user = Auth::user();
        $participants = Participant::where('added_by_user_id', $user->id)->paginate(12);
        
        // Get subscription information for participant limits
        $currentParticipantCount = $participants->total();
        $participantLimit = SubscriptionService::getParticipantLimit();
        $canAddParticipant = SubscriptionService::canAddParticipant($currentParticipantCount);
        
        // Get primary disability types for filter dropdown
        $primaryDisabilityTypes = Participant::distinct()->pluck('primary_disability')->filter()->sort()->toArray();
        
        // Get subscription status for the layout
        $subscriptionStatus = SubscriptionService::getSubscriptionStatus();
        
        return view('company.participants.matching.index', compact(
            'participants', 
            'canAddParticipant', 
            'currentParticipantCount', 
            'participantLimit',
            'primaryDisabilityTypes',
            'subscriptionStatus'
        ));
    }

    /**
     * Show participant matching interface
     */
    public function show(Participant $participant)
    {
        $user = Auth::user();
        
        // Ensure the participant belongs to the current provider
        if ($participant->added_by_user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to participant.');
        }

        $provider = $user->provider;

        if (!$provider) {
            return redirect()->route('provider.dashboard')->with('error', 'Provider profile not found. Please complete your provider profile first.');
        }

        // Get subscription status for the layout
        $subscriptionStatus = SubscriptionService::getSubscriptionStatus();

        // Get all stored matches for this participant (cumulative)
        $storedMatches = ParticipantMatch::forParticipant($participant->id)
            ->forProvider($provider->id)
            ->with(['matchedParticipant'])
            ->orderBy('compatibility_score', 'desc')
            ->get();

        return view('company.participants.matching.show', compact('participant', 'storedMatches', 'subscriptionStatus'));
    }

    /**
     * Find matches for a participant
     */
    public function findMatches(Participant $participant)
    {
        // Ensure the participant belongs to the current provider
        if ($participant->added_by_user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to participant.');
        }

        $user = Auth::user();
        $provider = $user->provider;

        if (!$provider) {
            return response()->json([
                'error' => 'Provider profile not found. Please complete your provider profile first.',
                'matches' => [],
                'total_matches' => 0,
                'new_matches_found' => 0,
            ], 400);
        }

        // Get all other participants (excluding the current one)
        $allParticipants = Participant::where('id', '!=', $participant->id)
            ->where('contact_for_suitable_match', true)
            ->get();

        // Calculate new matches
        $newMatches = $this->calculateMatches($participant, $allParticipants);

        // Store/update matches in database for cumulative matching
        $this->storeMatches($participant, $newMatches, $provider);

        // Get all stored matches for this participant (cumulative)
        $storedMatches = ParticipantMatch::forParticipant($participant->id)
            ->forProvider($provider->id)
            ->with(['matchedParticipant'])
            ->orderBy('compatibility_score', 'desc')
            ->get();

        // Format matches for frontend
        $formattedMatches = $storedMatches->map(function ($match) use ($participant) {
            $matchedParticipant = $match->matchedParticipant;
            
            // Check for existing match request between these two participants
            $matchRequest = MatchRequest::where(function($q) use ($participant, $matchedParticipant) {
                    $q->where('sender_participant_id', $participant->id)
                      ->where('receiver_participant_id', $matchedParticipant->id);
                })->orWhere(function($q) use ($participant, $matchedParticipant) {
                    $q->where('sender_participant_id', $matchedParticipant->id)
                      ->where('receiver_participant_id', $participant->id);
                })
                ->whereIn('status', ['pending', 'accepted'])
                ->first();
            
            // If accepted, check for conversation
            $conversationId = $match->conversation_id;
            if ($matchRequest && $matchRequest->status === 'accepted' && !$conversationId) {
                $conversation = Conversation::where(function($q) use ($matchRequest) {
                    $q->where('sender_participant_id', $matchRequest->sender_participant_id)
                      ->where('recipient_participant_id', $matchRequest->receiver_participant_id);
                })->orWhere(function($q) use ($matchRequest) {
                    $q->where('sender_participant_id', $matchRequest->receiver_participant_id)
                      ->where('recipient_participant_id', $matchRequest->sender_participant_id);
                })->first();
                
                if ($conversation) {
                    $conversationId = $conversation->id;
                }
            }
            
            return [
                'id' => $match->id,
                'participant' => $matchedParticipant,
                'score' => $match->compatibility_score,
                'compatibility_factors' => $match->compatibility_factors ?? [],
                'match_details' => $match->match_details ?? [],
                'status' => $match->status,
                'last_viewed_at' => $match->last_viewed_at,
                'contacted_at' => $match->contacted_at,
                'conversation_id' => $conversationId,
                'match_request_status' => $matchRequest ? $matchRequest->status : null,
                'match_request_id' => $matchRequest ? $matchRequest->id : null,
            ];
        });

        return response()->json([
            'participant' => $participant,
            'matches' => $formattedMatches,
            'total_matches' => $storedMatches->count(),
            'new_matches_found' => count($newMatches),
        ]);
    }

    /**
     * Calculate matches for a participant
     */
    private function calculateMatches(Participant $participant, $allParticipants)
    {
        $matches = [];

        foreach ($allParticipants as $potentialMatch) {
            $score = $this->matchingService->calculateCompatibilityScore($participant, $potentialMatch);
            
            // Include all matches regardless of score (like the original system)
                $matches[] = [
                'matched_participant_id' => $potentialMatch->id,
                'compatibility_score' => $score,
                'compatibility_factors' => $this->matchingService->getCompatibilityFactors($participant, $potentialMatch),
                'match_details' => $this->matchingService->getMatchDetails($participant, $potentialMatch),
            ];
        }

        // Sort by compatibility score (highest first)
        usort($matches, function($a, $b) {
            return $b['compatibility_score'] <=> $a['compatibility_score'];
        });

        return $matches;
    }

    /**
     * Store matches in database for cumulative matching
     */
    private function storeMatches(Participant $participant, $matches, $provider)
    {
        foreach ($matches as $matchData) {
            ParticipantMatch::updateOrCreate(
                [
                    'provider_id' => $provider->id,
                    'seeking_participant_id' => $participant->id,
                    'matched_participant_id' => $matchData['matched_participant_id'],
                ],
                [
                    'compatibility_score' => $matchData['compatibility_score'],
                    'compatibility_factors' => $matchData['compatibility_factors'],
                    'match_details' => $matchData['match_details'],
                    'status' => 'active', // Mark as active match
                ]
            );
        }
    }

    /**
     * Get participant details for matching
     */
    public function getParticipantDetails(Participant $participant)
    {
        if (!$participant->contact_for_suitable_match) {
            return response()->json(['error' => 'This participant is not available for matching.'], 403);
        }

        return response()->json([
            'participant' => [
                'participant_code_name' => $participant->participant_code_name,
                'age' => $participant->age,
                'gender_identity' => $participant->gender_identity,
                'primary_disability' => $participant->primary_disability,
                'secondary_disability' => $participant->secondary_disability,
                'estimated_support_hours_sil_level' => $participant->estimated_support_hours_sil_level,
                'night_support_type' => $participant->night_support_type,
                'state' => $participant->state,
                'suburb' => $participant->suburb,
                'smokes' => $participant->smokes,
                'pets_in_home_preference' => $participant->pets_in_home_preference,
                'preferred_number_of_housemates' => $participant->preferred_number_of_housemates,
                'housemate_preferences' => $participant->housemate_preferences,
                'good_home_environment_looks_like' => $participant->good_home_environment_looks_like,
                'interests_hobbies' => $participant->interests_hobbies,
                'cultural_religious_practices' => $participant->cultural_religious_practices,
                'daily_routine_preferences' => $participant->daily_routine_preferences,
                'communication_preferences' => $participant->communication_preferences,
                'personality_traits' => $participant->personality_traits,
                'move_in_availability' => $participant->move_in_availability,
            ]
        ]);
    }

    /**
     * Send message to participant owner (support coordinator or provider)
     */
    public function sendToOwner(Request $request, Participant $participant)
    {
        $request->validate([
            'content' => 'required|string|max:5000',
            'selected_participant_id' => 'required|exists:participants,id',
        ]);

        $user = Auth::user();
        $provider = $user->provider;

        if (!$provider) {
            return response()->json(['error' => 'Provider profile not found'], 400);
        }

        // Get the participant the provider is looking for a match for
        $matchingForParticipant = Participant::findOrFail($request->selected_participant_id);
        
        // Ensure the matching participant belongs to the provider
        if ($matchingForParticipant->added_by_user_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized access to participant'], 403);
        }

        // Find the target participant's owner (support coordinator or provider)
        $receiverUser = null;
        if ($participant->added_by_user_id) {
            $receiverUser = User::find($participant->added_by_user_id);
        } elseif ($participant->support_coordinator_id) {
            $receiverUser = $participant->supportCoordinator->user;
        }

        if (!$receiverUser) {
            return response()->json(['error' => 'Could not find participant owner'], 400);
        }

        // Check if user is admin - admins can bypass match request system
        if ($user->role === 'admin' || $user->role === 'super_admin') {
            // Admin can send messages directly - continue with existing logic
        } else {
            // Check if there's an accepted match request between these users
            $acceptedRequest = MatchRequest::where(function($query) use ($user, $receiverUser) {
                $query->where('sender_user_id', $user->id)
                      ->where('receiver_user_id', $receiverUser->id);
            })->orWhere(function($query) use ($user, $receiverUser) {
                $query->where('sender_user_id', $receiverUser->id)
                      ->where('receiver_user_id', $user->id);
            })->where('status', 'accepted')->first();

            if (!$acceptedRequest) {
                return response()->json([
                    'error' => 'You must send a match request first before starting a conversation',
                    'requires_match_request' => true
                ], 403);
            }
        }

        // Determine conversation type
        $conversationType = 'provider_to_' . ($receiverUser->role === 'coordinator' ? 'sc' : 'participant');

        // Determine which participant the provider is looking for a match for
        $matchingForParticipantId = null;
        if ($conversationType === 'provider_to_sc' || $conversationType === 'provider_to_participant') {
            // Use selected_participant_id if provided (from matching context), otherwise find the first participant that belongs to this provider
            if ($request->has('selected_participant_id')) {
                $matchingForParticipantId = $request->input('selected_participant_id');
            } else {
                $providerParticipant = Participant::where('added_by_user_id', $user->id)->first();
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
            ->where('initiator_user_id', $user->id)
            ->where('recipient_user_id', $receiverUser->id);

        if ($receiverUser->role === 'coordinator') {
            $conversationQuery->where('support_coordinator_id', $receiverUser->supportCoordinator?->id);
        }

        $conversation = $conversationQuery->first();

        if (!$conversation) {
            // Create new conversation with the specific matching_for_participant_id
            $conversation = Conversation::create([
                'type' => $conversationType,
                'provider_id' => $provider->id,
                'participant_id' => $participant->id,
                'matching_for_participant_id' => $matchingForParticipantId,
                'support_coordinator_id' => $receiverUser->role === 'coordinator' ? ($receiverUser->supportCoordinator?->id) : null,
                'initiator_user_id' => $user->id, // Provider who initiated
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
            'sender_id' => $user->id,
            'receiver_id' => $receiverUser->id,
            'content' => $request->input('content'),
            'type' => 'text',
            'original_sender_role' => $user->role,
            'original_recipient_role' => $receiverUser->role,
        ]);

        // Update participant match record
        ParticipantMatch::where('provider_id', $provider->id)
            ->where('seeking_participant_id', $matchingForParticipantId)
            ->where('matched_participant_id', $participant->id)
            ->update([
                'conversation_id' => $conversation->id,
                'contacted_at' => now(),
            ]);

        // Send notification
        $receiverUser->notify(new \App\Notifications\MessageReceived($message));

        return response()->json([
            'message' => 'Message sent successfully',
            'conversation_id' => $conversation->id,
        ]);
    }
}