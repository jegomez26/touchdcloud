<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Participant;
use App\Models\SupportTicket;
use App\Models\SupportCategory;
use App\Models\SupportTicketComment;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\MatchRequest;
use App\Services\ParticipantMatchingService;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\ParticipantProfileController;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class IndividualDashboardController extends Controller
{
    protected $matchingService;

    public function __construct(ParticipantMatchingService $matchingService)
    {
        $this->matchingService = $matchingService;
    }

    public function index(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        $participant = null;

        // Handle different user roles and representative status
        if ($user->role === 'participant') {
            // Direct participant user
            $participant = $user->participant;
        } elseif ($user->is_representative) {
            // Representative user - find their participant
            $participant = $user->participant;
        } else {
            // This should ideally not be reached if the above logic is sound,
            // but acts as a final safety net.
            Log::error('IndividualDashboardController: No participant found despite user role/representative status.', ['user_id' => $user->id, 'user_role' => $user->role, 'is_representative' => $user->is_representative]);
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('login')->with('error', 'Could not find a participant profile. Please contact support.');
        }

        $profileController = new ParticipantProfileController();
        $basicDetailsComplete = $profileController->isBasicDetailsComplete($participant);
        $profileCompletionPercentage = $profileController->calculateProfileCompletion($participant);

        $applyingCoordinators = collect(); // Default to empty collection
        $latestMessages = collect(); // Default to empty collection

        if ($basicDetailsComplete) {
            // Your logic for fetching applying coordinators and latest messages
            // (uncomment and ensure models are imported as needed)
        } else {
            if ($user->profile_completed && !$participant->id) {
                $user->update(['profile_completed' => false]);
            }
        }

        return view('indiv.main-content', compact(
            'basicDetailsComplete',
            'profileCompletionPercentage',
            'applyingCoordinators',
            'latestMessages',
            'participant',
            
        ));
    }

    public function possibleMatches()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        $participant = $user->participant;

        if (!$participant) {
            abort(403, 'Participant profile not found.');
        }

        // Get all other participants (excluding the current one) - including those under SCs and providers
        $allParticipants = Participant::where('id', '!=', $participant->id)
            ->whereNotNull('participant_code_name')
            ->with(['user', 'supportCoordinator.user', 'addedByUser.provider']) // Eager load all possible user relationships
            ->get();

        Log::info('Possible matches data - showing ALL participants with code names', [
            'current_participant_id' => $participant->id,
            'current_participant_user_id' => $participant->user_id,
            'total_participants_found' => $allParticipants->count(),
            'participants_with_direct_users' => $allParticipants->filter(function($p) { return $p->user !== null; })->count(),
            'participants_under_scs' => $allParticipants->filter(function($p) { return $p->supportCoordinator !== null; })->count(),
            'participants_added_by_users' => $allParticipants->filter(function($p) { return $p->addedByUser !== null; })->count(),
            'participants_wanting_contact' => $allParticipants->filter(function($p) { return $p->contact_for_suitable_match; })->count(),
            'participant_ids' => $allParticipants->pluck('id')->toArray(),
            'participant_details' => $allParticipants->map(function($p) {
                return [
                    'id' => $p->id,
                    'code_name' => $p->participant_code_name,
                    'contact_for_match' => $p->contact_for_suitable_match,
                    'has_direct_user' => $p->user !== null,
                    'has_sc' => $p->supportCoordinator !== null,
                    'added_by_user' => $p->addedByUser !== null
                ];
            })->toArray()
        ]);

        $matches = $this->calculateMatches($participant, $allParticipants);

        return view('indiv.possible-matches', compact('participant', 'matches'));
    }

    /**
     * Find potential matches for the authenticated participant
     */
    public function findMatches()
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $user = Auth::user();
        $participant = $user->participant;

        if (!$participant) {
            return response()->json(['error' => 'Participant profile not found'], 403);
        }

        // Get all other participants (excluding the current one) - including those under SCs and providers
        $allParticipants = Participant::where('id', '!=', $participant->id)
            ->whereNotNull('participant_code_name')
            ->with(['user', 'supportCoordinator.user', 'addedByUser.provider']) // Eager load all possible user relationships
            ->get();

        $matches = $this->calculateMatches($participant, $allParticipants);

        return response()->json([
            'participant' => $participant,
            'matches' => $matches
        ]);
    }

    /**
     * Calculate compatibility scores for potential matches
     */
    private function calculateMatches(Participant $participant, $allParticipants)
    {
        $matches = [];

        foreach ($allParticipants as $potentialMatch) {
            $score = $this->matchingService->calculateCompatibilityScore($participant, $potentialMatch);
            
            // Check if conversation already exists between these participants
            $existingConversation = $this->checkExistingConversation($participant, $potentialMatch);
            
            $matches[] = [
                'participant' => $potentialMatch,
                'score' => $score,
                'compatibility_factors' => $this->matchingService->getCompatibilityFactors($participant, $potentialMatch),
                'has_conversation' => $existingConversation !== null,
                'conversation_id' => $existingConversation ? $existingConversation->id : null,
                'last_message_at' => $existingConversation ? $existingConversation->last_message_at : null,
                'unread_count' => $existingConversation ? $this->getUnreadMessageCount($existingConversation, $participant->user_id) : 0
            ];
        }

        // Sort by compatibility score (highest first), then by conversation status
        usort($matches, function($a, $b) {
            // First sort by score
            $scoreComparison = $b['score'] <=> $a['score'];
            if ($scoreComparison !== 0) {
                return $scoreComparison;
            }
            
            // Then prioritize matches with existing conversations
            return $b['has_conversation'] <=> $a['has_conversation'];
        });

        return $matches;
    }

    /**
     * Check if a conversation already exists between two participants
     */
    private function checkExistingConversation(Participant $participant1, Participant $participant2)
    {
        // Check for existing conversation between these participants
        return Conversation::where(function($query) use ($participant1, $participant2) {
            $query->where(function($q) use ($participant1, $participant2) {
                $q->where('participant_id', $participant1->id)
                  ->where('matching_for_participant_id', $participant2->id);
            })
            ->orWhere(function($q) use ($participant1, $participant2) {
                $q->where('participant_id', $participant2->id)
                  ->where('matching_for_participant_id', $participant1->id);
            });
        })->first();
    }

    /**
     * Get unread message count for a conversation
     */
    private function getUnreadMessageCount($conversation, $userId)
    {
        return $conversation->messages()
            ->where('receiver_id', $userId)
            ->whereNull('read_at')
            ->count();
    }

    /**
     * Get participant details for the view details modal
     */
    public function getParticipantDetails(Participant $participant)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $user = Auth::user();
        $currentParticipant = $user->participant;

        if (!$currentParticipant) {
            return response()->json(['error' => 'Participant profile not found'], 403);
        }

        // Return participant details (anonymized - only showing code name)
        return response()->json([
            'success' => true,
            'participant' => [
                'participant_code_name' => $participant->participant_code_name,
                'age' => $participant->age,
                'gender' => $participant->gender,
                'date_of_birth' => $participant->date_of_birth ? \Carbon\Carbon::parse($participant->date_of_birth)->format('d/m/Y') : null,
                'primary_disability' => $participant->primary_disability,
                'secondary_disability' => $participant->secondary_disability,
                'estimated_support_hours_sil_level' => $participant->estimated_support_hours_sil_level,
                'night_support_type' => $participant->night_support_type,
                'suburb' => $participant->suburb,
                'state' => $participant->state,
                'move_in_availability' => $participant->move_in_availability,
                'interests_hobbies' => $participant->interests_hobbies,
                'cultural_religious_practices' => $participant->cultural_religious_practices,
            ]
        ]);
    }

    /**
     * Send message to another participant
     */
    public function sendMessage(Request $request, Participant $participant)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $user = Auth::user();
        $currentParticipant = $user->participant;

        if (!$currentParticipant) {
            return response()->json(['error' => 'Participant profile not found'], 403);
        }

        $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        // Check if participant is seeking matches
        if (!$participant->contact_for_suitable_match) {
            return response()->json(['error' => 'This participant is not currently seeking matches'], 403);
        }

        // Check if user is admin - admins can bypass match request system
        if ($user->role === 'admin' || $user->role === 'super_admin') {
            // Admin can send messages directly
            return $this->createConversationAndSendMessage($request, $user, $currentParticipant, $participant);
        }

        // Check if there's an accepted match request between these users
        $acceptedRequest = MatchRequest::where(function($query) use ($user, $participant) {
            $query->where('sender_user_id', $user->id)
                  ->where('receiver_user_id', $participant->user_id);
        })->orWhere(function($query) use ($user, $participant) {
            $query->where('sender_user_id', $participant->user_id)
                  ->where('receiver_user_id', $user->id);
        })->where('status', 'accepted')->first();

        if (!$acceptedRequest) {
            return response()->json([
                'error' => 'You must send a match request first before starting a conversation',
                'requires_match_request' => true
            ], 403);
        }

        return $this->createConversationAndSendMessage($request, $user, $currentParticipant, $participant);
    }

    /**
     * Helper method to create conversation and send message
     */
    private function createConversationAndSendMessage(Request $request, User $user, Participant $currentParticipant, Participant $participant)
    {
        try {
            // Find or create conversation
            $conversation = Conversation::where(function($query) use ($currentParticipant, $participant) {
                $query->where(function($q) use ($currentParticipant, $participant) {
                    $q->where('participant_id', $currentParticipant->id)
                      ->where('matching_for_participant_id', $participant->id);
                })
                ->orWhere(function($q) use ($currentParticipant, $participant) {
                    $q->where('participant_id', $participant->id)
                      ->where('matching_for_participant_id', $currentParticipant->id);
                });
            })->first();

            if (!$conversation) {
                // Create new conversation
                $conversation = Conversation::create([
                    'type' => 'participant_to_participant',
                    'participant_id' => $currentParticipant->id,
                    'matching_for_participant_id' => $participant->id,
                    'initiator_user_id' => $user->id,
                    'recipient_user_id' => $participant->user_id,
                    'initiator_participant_id' => $currentParticipant->id,
                    'recipient_participant_id' => $participant->id,
                    'last_message_at' => now(),
                ]);
            } else {
                $conversation->update(['last_message_at' => now()]);
            }

            // Create message
            Message::create([
                'conversation_id' => $conversation->id,
                'sender_id' => $user->id,
                'receiver_id' => $participant->user_id,
                'content' => $request->content,
                'sent_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Message sent successfully',
                'conversation_id' => $conversation->id
            ]);

        } catch (\Exception $e) {
            Log::error('Error sending message from individual participant', [
                'error' => $e->getMessage(),
                'current_participant_id' => $currentParticipant->id,
                'target_participant_id' => $participant->id,
                'user_id' => $user->id
            ]);

            return response()->json(['error' => 'Failed to send message'], 500);
        }
    }

    /**
     * Get pending match requests for the current user
     */
    public function getPendingMatchRequests()
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

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
     * Show match requests page for participants
     */
    public function matchRequests()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        
        // Get pending requests (received)
        $pendingRequests = MatchRequest::pendingForUser($user->id)
            ->with(['senderUser', 'senderParticipant'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Get sent requests
        $sentRequests = MatchRequest::sentByUser($user->id)
            ->with(['receiverUser', 'receiverParticipant'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Get accepted requests (both sent and received)
        $acceptedRequests = MatchRequest::where(function($query) use ($user) {
                $query->where('sender_user_id', $user->id)
                      ->orWhere('receiver_user_id', $user->id);
            })
            ->where('status', 'accepted')
            ->with(['senderUser', 'receiverUser', 'senderParticipant', 'receiverParticipant'])
            ->orderBy('responded_at', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        $pendingCount = $pendingRequests->count();

        return view('indiv.match-requests', compact('pendingRequests', 'sentRequests', 'acceptedRequests', 'pendingCount'));
    }

    public function supportCenter(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        
        $query = SupportTicket::with(['category', 'assignedAdmin'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc');
        
        // Filter by status
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }
        
        // Filter by priority
        if ($request->has('priority') && $request->priority !== 'all') {
            $query->where('priority', $request->priority);
        }
        
        // Search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('ticket_number', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        
        $tickets = $query->paginate(10);
        
        // Statistics
        $totalTickets = SupportTicket::where('user_id', $user->id)->count();
        $openTickets = SupportTicket::where('user_id', $user->id)->where('status', 'open')->count();
        $inProgressTickets = SupportTicket::where('user_id', $user->id)->where('status', 'in_progress')->count();
        $resolvedTickets = SupportTicket::where('user_id', $user->id)->where('status', 'resolved')->count();
        
        // Get categories for new ticket form
        $categories = SupportCategory::active()->ordered()->get();

        return view('indiv.support-center.index', compact(
            'tickets', 
            'totalTickets', 
            'openTickets', 
            'inProgressTickets', 
            'resolvedTickets',
            'categories'
        ));
    }

    public function createTicket(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:2000',
            'type' => 'required|in:bug_report,feature_request,technical_issue,account_issue,billing_question,general_inquiry,complaint',
            'priority' => 'required|in:low,medium,high,urgent',
            'category_id' => 'nullable|exists:support_categories,id',
        ]);
        
        $ticket = SupportTicket::create([
            'ticket_number' => SupportTicket::generateTicketNumber(),
            'title' => $request->title,
            'description' => $request->description,
            'type' => $request->type,
            'priority' => $request->priority,
            'status' => 'open',
            'user_id' => Auth::id(),
            'category_id' => $request->category_id,
        ]);
        
        return redirect()->route('indiv.support-center.index')
            ->with('success', "Ticket {$ticket->ticket_number} created successfully!");
    }

    public function viewTicket(SupportTicket $ticket)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // Ensure user can only view their own tickets
        if ($ticket->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to ticket.');
        }

        $ticket->load(['category', 'assignedAdmin', 'comments.user']);
        $categories = SupportCategory::active()->ordered()->get();

        return view('indiv.support-center.view', compact('ticket', 'categories'));
    }

    public function addComment(Request $request, SupportTicket $ticket)
    {
        // Ensure user can only comment on their own tickets
        if ($ticket->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to ticket.');
        }
        
        $request->validate([
            'comment' => 'required|string|max:1000',
        ]);
        
        SupportTicketComment::create([
            'support_ticket_id' => $ticket->id,
            'user_id' => Auth::id(),
            'comment' => $request->comment,
            'is_internal' => false,
            'is_admin_reply' => false,
        ]);
        
        return redirect()->route('indiv.support-center.view', $ticket)
            ->with('success', 'Comment added successfully!');
    }
}