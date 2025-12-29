<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Provider; // Ensure you import your Provider model
use App\Models\Participant; // Ensure you import your Participant model
use App\Models\SupportTicket;
use App\Models\SupportCategory;
use App\Models\SupportTicketComment;
use App\Models\MatchRequest;
use Illuminate\Support\Facades\DB;

class ProviderDashboardController extends Controller
{
    public function index()
    {
        // Load the authenticated user's associated provider profile
        $provider = Auth::user()->provider;
        
        if (!$provider) {
            return redirect()->route('provider.create')->with('error', 'Please complete your provider profile first.');
        }

        // Get subscription status
        $subscriptionStatus = \App\Services\SubscriptionService::getSubscriptionStatus();

        // Get participants data for charts and maps
        $participants = Auth::user()->participantsAdded();
        
        // Get participant limit information
        $currentParticipantCount = $participants->count();
        $canAddParticipant = \App\Services\SubscriptionService::canAddParticipant($currentParticipantCount);
        $participantLimit = \App\Services\SubscriptionService::getParticipantLimit();
        
        // Get accommodation limit information
        $currentAccommodationCount = $provider->properties()->count();
        $canAddAccommodation = \App\Services\SubscriptionService::canAddAccommodation($currentAccommodationCount);
        $accommodationLimit = \App\Services\SubscriptionService::getAccommodationLimit();
        
        // Participants by gender (include all participants, handle missing data)
        $participantsByGender = $participants->selectRaw('gender_identity, COUNT(*) as count')
            ->groupBy('gender_identity')
            ->get()
            ->map(function($item) {
                $gender = $item->gender_identity;
                if (empty($gender) || is_null($gender)) {
                    $gender = 'Not Specified';
                } else {
                    $gender = trim(ucfirst(strtolower($gender)));
                }
                
                return [
                    'gender_identity' => $gender,
                    'count' => $item->count
                ];
            })
            ->groupBy('gender_identity')
            ->map(function($group) {
                return [
                    'gender_identity' => $group->first()['gender_identity'],
                    'count' => $group->sum('count')
                ];
            })
            ->values();

        // Participants by disability (include all participants, handle missing data)
        $participantsByDisability = $participants->selectRaw('primary_disability, COUNT(*) as count')
            ->groupBy('primary_disability')
            ->get()
            ->map(function($item) {
                $disability = $item->primary_disability;
                if (empty($disability) || is_null($disability)) {
                    $disability = 'Not Specified';
                }
                
                return [
                    'primary_disability' => $disability,
                    'count' => $item->count
                ];
            });

        // Participants by state (include all participants, handle missing data)
        $participantsByState = $participants->selectRaw('state, COUNT(*) as count')
            ->groupBy('state')
            ->get()
            ->map(function($item) {
                $state = $item->state;
                if (empty($state) || is_null($state)) {
                    $state = 'Not Specified';
                } else {
                    $state = strtoupper(trim($state));
                }
                
                return [
                    'state' => $state,
                    'count' => $item->count
                ];
            })
            ->groupBy('state')
            ->map(function($group) {
                return [
                    'state' => $group->first()['state'],
                    'count' => $group->sum('count')
                ];
            })
            ->values();

        // Participants by suburb with coordinates
        $participantsBySuburb = $participants->selectRaw('suburb, COUNT(*) as count')
            ->whereNotNull('suburb')
            ->where('suburb', '!=', '')
            ->groupBy('suburb')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get()
            ->map(function($item) {
                // Mock coordinates - in production, you'd fetch these from a suburbs/coordinates table
                $mockCoordinates = [
                    'Sydney' => ['latitude' => -33.8688, 'longitude' => 151.2093],
                    'Melbourne' => ['latitude' => -37.8136, 'longitude' => 144.9631],
                    'Brisbane' => ['latitude' => -27.4698, 'longitude' => 153.0251],
                    'Perth' => ['latitude' => -31.9505, 'longitude' => 115.8605],
                    'Adelaide' => ['latitude' => -34.9285, 'longitude' => 138.6007],
                    'Hobart' => ['latitude' => -42.8821, 'longitude' => 147.3272],
                    'Darwin' => ['latitude' => -12.4634, 'longitude' => 130.8456],
                    'Canberra' => ['latitude' => -35.2809, 'longitude' => 149.1300]
                ];
                
                $item->latitude = $mockCoordinates[$item->suburb]['latitude'] ?? null;
                $item->longitude = $mockCoordinates[$item->suburb]['longitude'] ?? null;
                
                return $item;
            });

        // Participants by age range (create fresh query to avoid conflicts)
        $ageRangeData = Auth::user()->participantsAdded()->select(
            DB::raw("SUM(CASE WHEN date_of_birth IS NOT NULL AND TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) BETWEEN 18 AND 25 THEN 1 ELSE 0 END) as age_18_25"),
            DB::raw("SUM(CASE WHEN date_of_birth IS NOT NULL AND TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) BETWEEN 26 AND 35 THEN 1 ELSE 0 END) as age_26_35"),
            DB::raw("SUM(CASE WHEN date_of_birth IS NOT NULL AND TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) BETWEEN 36 AND 50 THEN 1 ELSE 0 END) as age_36_50"),
            DB::raw("SUM(CASE WHEN date_of_birth IS NOT NULL AND TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) > 50 THEN 1 ELSE 0 END) as age_51_plus"),
            DB::raw("SUM(CASE WHEN date_of_birth IS NULL THEN 1 ELSE 0 END) as age_unknown")
        )->first();

        $participantsByAgeRange = collect([
            ['age_range' => '18-25', 'count' => $ageRangeData->age_18_25 ?? 0],
            ['age_range' => '26-35', 'count' => $ageRangeData->age_26_35 ?? 0],
            ['age_range' => '36-50', 'count' => $ageRangeData->age_36_50 ?? 0],
            ['age_range' => '51+', 'count' => $ageRangeData->age_51_plus ?? 0],
            ['age_range' => 'Unknown', 'count' => $ageRangeData->age_unknown ?? 0]
        ])->filter(function($item) {
            return $item['count'] > 0;
        });

        // Gender distribution data (include all participants, handle missing data)
        $genderDistributionData = $participants->selectRaw('gender_identity, COUNT(*) as count')
            ->groupBy('gender_identity')
            ->get()
            ->map(function($item) {
                // Normalize gender values
                $gender = $item->gender_identity;
                if (empty($gender) || is_null($gender)) {
                    $normalizedGender = 'Not Specified';
                } else {
                    $normalizedGender = trim(ucfirst(strtolower($gender)));
                    
                    // Handle common variations
                    if (in_array($normalizedGender, ['M', 'Male', 'MALE'])) {
                        $normalizedGender = 'Male';
                    } elseif (in_array($normalizedGender, ['F', 'Female', 'FEMALE'])) {
                        $normalizedGender = 'Female';
                    } elseif (in_array($normalizedGender, ['Other', 'Non-binary', 'Nonbinary', 'Non binary'])) {
                        $normalizedGender = 'Other';
                    }
                }
                
                return [
                    'gender' => $normalizedGender,
                    'count' => $item->count
                ];
            })
            ->groupBy('gender')
            ->map(function($group) {
                return [
                    'gender' => $group->first()['gender'],
                    'count' => $group->sum('count')
                ];
            })
            ->values()
            ->filter(function($item) {
                return $item['count'] > 0;
            });

        // Calculate occupancy rate
        $accommodations = $provider->properties();
        $occupiedAccommodations = $accommodations->where('status', 'occupied')->count();
        $totalAccommodations = $currentAccommodationCount;
        $occupancyRate = $totalAccommodations > 0 ? round(($occupiedAccommodations / $totalAccommodations) * 100) : 0;

        // Recent Activities - Enhanced with more activity types
        $recentActivities = collect();
        
        // Get recent participants (last 7 days)
        $recentParticipants = $participants->where('created_at', '>=', now()->subDays(7))->get();
        foreach ($recentParticipants as $participant) {
            if ($participant->created_at) {
                $recentActivities->push([
                    'description' => 'New participant added: ' . $participant->first_name . ' ' . $participant->last_name,
                    'time' => $participant->created_at->diffForHumans(),
                    'type' => 'participant',
                    'created_at' => $participant->created_at
                ]);
            }
        }
        
        // Get recent accommodations (last 7 days)
        $recentAccommodations = $provider->properties()->where('created_at', '>=', now()->subDays(7))->get();
        foreach ($recentAccommodations as $accommodation) {
            if ($accommodation->created_at) {
                $recentActivities->push([
                    'description' => 'New accommodation listed: ' . $accommodation->title,
                    'time' => $accommodation->created_at->diffForHumans(),
                    'type' => 'accommodation',
                    'created_at' => $accommodation->created_at
                ]);
            }
        }
        
        // Get recent accommodation updates (last 7 days)
        $recentAccommodationUpdates = $provider->properties()->where('updated_at', '>=', now()->subDays(7))
            ->where('updated_at', '>', DB::raw('created_at'))->get();
        foreach ($recentAccommodationUpdates as $accommodation) {
            if ($accommodation->updated_at) {
                $recentActivities->push([
                    'description' => 'Accommodation updated: ' . $accommodation->title,
                    'time' => $accommodation->updated_at->diffForHumans(),
                    'type' => 'accommodation_update',
                    'created_at' => $accommodation->updated_at
                ]);
            }
        }
        
        // Get recent enquiries (last 7 days)
        $recentEnquiries = \App\Models\Enquiry::whereHas('property', function($q) use ($provider) {
            $q->where('provider_id', $provider->id);
        })->where('created_at', '>=', now()->subDays(7))->get();
        
        foreach ($recentEnquiries as $enquiry) {
            $recentActivities->push([
                'description' => 'New enquiry received for: ' . $enquiry->property->title,
                'time' => $enquiry->created_at->diffForHumans(),
                'type' => 'enquiry',
                'created_at' => $enquiry->created_at
            ]);
        }
        
        // Get recent enquiry status updates (last 7 days)
        $recentEnquiryUpdates = \App\Models\Enquiry::whereHas('property', function($q) use ($provider) {
            $q->where('provider_id', $provider->id);
        })->where('updated_at', '>=', now()->subDays(7))
        ->where('updated_at', '>', DB::raw('created_at'))->get();
        
        foreach ($recentEnquiryUpdates as $enquiry) {
            $recentActivities->push([
                'description' => 'Enquiry status updated: ' . ucfirst($enquiry->status) . ' - ' . $enquiry->property->title,
                'time' => $enquiry->updated_at->diffForHumans(),
                'type' => 'enquiry_update',
                'created_at' => $enquiry->updated_at
            ]);
        }
        
        // Get recent messages sent (last 7 days)
        $recentMessagesSent = \App\Models\Message::where('sender_id', Auth::id())
            ->where('created_at', '>=', now()->subDays(7))->get();
        
        foreach ($recentMessagesSent as $message) {
            $recentActivities->push([
                'description' => 'Message sent to participant owner',
                'time' => $message->created_at->diffForHumans(),
                'type' => 'message_sent',
                'created_at' => $message->created_at
            ]);
        }
        
        // Get recent messages received (last 7 days)
        $recentMessagesReceived = \App\Models\Message::whereHas('conversation', function($q) use ($provider) {
            $q->where('provider_id', $provider->id);
        })->where('sender_id', '!=', Auth::id())
        ->where('created_at', '>=', now()->subDays(7))->get();
        
        foreach ($recentMessagesReceived as $message) {
            $recentActivities->push([
                'description' => 'New message received',
                'time' => $message->created_at->diffForHumans(),
                'type' => 'message_received',
                'created_at' => $message->created_at
            ]);
        }
        
        // Get recent participant matches found (last 7 days)
        $recentMatches = \App\Models\ParticipantMatch::where('provider_id', $provider->id)
            ->where('created_at', '>=', now()->subDays(7))->get();
        
        foreach ($recentMatches as $match) {
            $recentActivities->push([
                'description' => 'New participant match found: ' . $match->compatibility_score . '% compatibility',
                'time' => $match->created_at->diffForHumans(),
                'type' => 'match_found',
                'created_at' => $match->created_at
            ]);
        }
        
        // Get recent support tickets (last 7 days)
        $recentSupportTickets = \App\Models\SupportTicket::where('user_id', Auth::id())
            ->where('created_at', '>=', now()->subDays(7))->get();
        
        foreach ($recentSupportTickets as $ticket) {
            $recentActivities->push([
                'description' => 'Support ticket created: ' . $ticket->subject,
                'time' => $ticket->created_at->diffForHumans(),
                'type' => 'support_ticket',
                'created_at' => $ticket->created_at
            ]);
        }
        
        // Get recent support ticket updates (last 7 days)
        $recentSupportTicketUpdates = \App\Models\SupportTicket::where('user_id', Auth::id())
            ->where('updated_at', '>=', now()->subDays(7))
            ->where('updated_at', '>', DB::raw('created_at'))->get();
        
        foreach ($recentSupportTicketUpdates as $ticket) {
            $recentActivities->push([
                'description' => 'Support ticket updated: ' . $ticket->subject,
                'time' => $ticket->updated_at->diffForHumans(),
                'type' => 'support_update',
                'created_at' => $ticket->updated_at
            ]);
        }
        
        // Get recent profile updates (last 7 days)
        $recentProfileUpdates = \App\Models\User::where('id', Auth::id())
            ->where('updated_at', '>=', now()->subDays(7))
            ->where('updated_at', '>', DB::raw('created_at'))->get();
        
        foreach ($recentProfileUpdates as $user) {
            $recentActivities->push([
                'description' => 'Profile information updated',
                'time' => $user->updated_at->diffForHumans(),
                'type' => 'profile_update',
                'created_at' => $user->updated_at
            ]);
        }
        
        // Sort by creation time and take the 8 most recent (increased from 5)
        $recentActivities = $recentActivities->sortByDesc('created_at')->take(8);

        // Match Statistics
        $totalMatches = \App\Models\ParticipantMatch::where('provider_id', $provider->id)->count();
        $newMatchesToday = \App\Models\ParticipantMatch::where('provider_id', $provider->id)
            ->whereDate('created_at', today())->count();

        // Accepted Match Request Statistics
        $user = Auth::user();
        $acceptedMatchRequests = MatchRequest::where('status', 'accepted')
            ->where(function($query) use ($user) {
                $query->where('sender_user_id', $user->id)
                      ->orWhere('receiver_user_id', $user->id);
            })
            ->count();

        // System-wide Analytics (all participants in the system)
        $systemWideAnalytics = $this->getSystemWideAnalytics();

        return view('company.dashboard-content', compact(
            'provider', 
            'subscriptionStatus', 
            'participantsByGender', 
            'participantsByDisability', 
            'participantsByState',
            'participantsBySuburb',
            'participantsByAgeRange',
            'canAddParticipant',
            'participantLimit',
            'currentParticipantCount',
            'canAddAccommodation',
            'accommodationLimit',
            'currentAccommodationCount',
            'totalMatches',
            'newMatchesToday',
            'acceptedMatchRequests',
            'genderDistributionData',
            'recentActivities',
            'systemWideAnalytics'
        ));
    }

    /**
     * Get system-wide analytics for all participants
     */
    private function getSystemWideAnalytics()
    {
        // Top 10 suburbs with most participants
        $topSuburbs = Participant::selectRaw('suburb, COUNT(*) as count')
            ->whereNotNull('suburb')
            ->where('suburb', '!=', '')
            ->groupBy('suburb')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get()
            ->map(function($item) {
                // Add coordinates for mapping
                $mockCoordinates = [
                    'Sydney' => ['latitude' => -33.8688, 'longitude' => 151.2093],
                    'Melbourne' => ['latitude' => -37.8136, 'longitude' => 144.9631],
                    'Brisbane' => ['latitude' => -27.4698, 'longitude' => 153.0251],
                    'Perth' => ['latitude' => -31.9505, 'longitude' => 115.8605],
                    'Adelaide' => ['latitude' => -34.9285, 'longitude' => 138.6007],
                    'Hobart' => ['latitude' => -42.8821, 'longitude' => 147.3272],
                    'Darwin' => ['latitude' => -12.4634, 'longitude' => 130.8456],
                    'Canberra' => ['latitude' => -35.2809, 'longitude' => 149.1300],
                    'Gold Coast' => ['latitude' => -28.0167, 'longitude' => 153.4000],
                    'Newcastle' => ['latitude' => -32.9283, 'longitude' => 151.7817],
                    'Wollongong' => ['latitude' => -34.4278, 'longitude' => 150.8931],
                    'Geelong' => ['latitude' => -38.1499, 'longitude' => 144.3617],
                    'Townsville' => ['latitude' => -19.2590, 'longitude' => 146.8169],
                    'Cairns' => ['latitude' => -16.9186, 'longitude' => 145.7781],
                    'Toowoomba' => ['latitude' => -27.5598, 'longitude' => 151.9507],
                    'Ballarat' => ['latitude' => -37.5622, 'longitude' => 143.8503],
                    'Bendigo' => ['latitude' => -36.7570, 'longitude' => 144.2792],
                    'Albury' => ['latitude' => -36.0737, 'longitude' => 146.9135],
                    'Launceston' => ['latitude' => -41.4332, 'longitude' => 147.1441],
                    'Mackay' => ['latitude' => -21.1535, 'longitude' => 149.1865]
                ];
                
                $item->latitude = $mockCoordinates[$item->suburb]['latitude'] ?? null;
                $item->longitude = $mockCoordinates[$item->suburb]['longitude'] ?? null;
                
                return $item;
            });

        // Top 10 states with most participants
        $topStates = Participant::selectRaw('state, COUNT(*) as count')
            ->whereNotNull('state')
            ->where('state', '!=', '')
            ->groupBy('state')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get()
            ->map(function($item) {
                return [
                    'state' => strtoupper(trim($item->state)),
                    'count' => $item->count
                ];
            });

        // Top 10 disabilities with most participants
        $topDisabilities = Participant::selectRaw('primary_disability, COUNT(*) as count')
            ->whereNotNull('primary_disability')
            ->where('primary_disability', '!=', '')
            ->groupBy('primary_disability')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get();

        // All participants grouped by suburb for mapping (with coordinates and counts)
        $allParticipants = Participant::select('suburb', 'state', 'primary_disability', 'gender_identity')
            ->get()
            ->groupBy(function($participant) {
                $suburb = trim($participant->suburb);
                $state = trim($participant->state);
                
                // Create a location key for grouping
                if (!empty($suburb)) {
                    return $suburb . ', ' . $state;
                } elseif (!empty($state)) {
                    return 'State: ' . $state;
                } else {
                    return 'Unknown Location';
                }
            })
            ->map(function($participants, $locationKey) {
                $firstParticipant = $participants->first();
                $suburb = trim($firstParticipant->suburb);
                $state = trim($firstParticipant->state);
                // Add coordinates for mapping
                $mockCoordinates = [
                    'Sydney' => ['latitude' => -33.8688, 'longitude' => 151.2093],
                    'Melbourne' => ['latitude' => -37.8136, 'longitude' => 144.9631],
                    'Brisbane' => ['latitude' => -27.4698, 'longitude' => 153.0251],
                    'Perth' => ['latitude' => -31.9505, 'longitude' => 115.8605],
                    'Adelaide' => ['latitude' => -34.9285, 'longitude' => 138.6007],
                    'Hobart' => ['latitude' => -42.8821, 'longitude' => 147.3272],
                    'Darwin' => ['latitude' => -12.4634, 'longitude' => 130.8456],
                    'Canberra' => ['latitude' => -35.2809, 'longitude' => 149.1300],
                    'Gold Coast' => ['latitude' => -28.0167, 'longitude' => 153.4000],
                    'Newcastle' => ['latitude' => -32.9283, 'longitude' => 151.7817],
                    'Wollongong' => ['latitude' => -34.4278, 'longitude' => 150.8931],
                    'Geelong' => ['latitude' => -38.1499, 'longitude' => 144.3617],
                    'Townsville' => ['latitude' => -19.2590, 'longitude' => 146.8169],
                    'Cairns' => ['latitude' => -16.9186, 'longitude' => 145.7781],
                    'Toowoomba' => ['latitude' => -27.5598, 'longitude' => 151.9507],
                    'Ballarat' => ['latitude' => -37.5622, 'longitude' => 143.8503],
                    'Bendigo' => ['latitude' => -36.7570, 'longitude' => 144.2792],
                    'Albury' => ['latitude' => -36.0737, 'longitude' => 146.9135],
                    'Launceston' => ['latitude' => -41.4332, 'longitude' => 147.1441],
                    'Mackay' => ['latitude' => -21.1535, 'longitude' => 149.1865],
                    // Additional suburbs
                    'Parramatta' => ['latitude' => -33.8148, 'longitude' => 151.0019],
                    'Blacktown' => ['latitude' => -33.7686, 'longitude' => 150.9108],
                    'Liverpool' => ['latitude' => -33.9249, 'longitude' => 150.9259],
                    'Penrith' => ['latitude' => -33.7500, 'longitude' => 150.7000],
                    'Richmond' => ['latitude' => -33.6000, 'longitude' => 150.7500],
                    'Campbelltown' => ['latitude' => -34.0667, 'longitude' => 150.8167],
                    'Bankstown' => ['latitude' => -33.9167, 'longitude' => 151.0333],
                    'Fairfield' => ['latitude' => -33.8667, 'longitude' => 150.9500],
                    'Hurstville' => ['latitude' => -33.9667, 'longitude' => 151.1000],
                    'Chatswood' => ['latitude' => -33.8000, 'longitude' => 151.1833],
                    'Manly' => ['latitude' => -33.8000, 'longitude' => 151.2833],
                    'Bondi' => ['latitude' => -33.8915, 'longitude' => 151.2767],
                    'Surry Hills' => ['latitude' => -33.8889, 'longitude' => 151.2083],
                    'Redfern' => ['latitude' => -33.8933, 'longitude' => 151.2042],
                    'Glebe' => ['latitude' => -33.8750, 'longitude' => 151.1833],
                    'Newtown' => ['latitude' => -33.8978, 'longitude' => 151.1806],
                    'Marrickville' => ['latitude' => -33.9167, 'longitude' => 151.1500],
                    'Leichhardt' => ['latitude' => -33.8833, 'longitude' => 151.1500],
                    'Balmain' => ['latitude' => -33.8667, 'longitude' => 151.1833],
                    'Rozelle' => ['latitude' => -33.8667, 'longitude' => 151.1667],
                    'Annandale' => ['latitude' => -33.8833, 'longitude' => 151.1667],
                    'Camperdown' => ['latitude' => -33.8833, 'longitude' => 151.1833],
                    'Ultimo' => ['latitude' => -33.8833, 'longitude' => 151.2000],
                    'Pyrmont' => ['latitude' => -33.8667, 'longitude' => 151.2000],
                    'Darlinghurst' => ['latitude' => -33.8750, 'longitude' => 151.2167],
                    'Kings Cross' => ['latitude' => -33.8750, 'longitude' => 151.2250],
                    'Potts Point' => ['latitude' => -33.8667, 'longitude' => 151.2250],
                    'Woolloomooloo' => ['latitude' => -33.8667, 'longitude' => 151.2167],
                    'Elizabeth Bay' => ['latitude' => -33.8667, 'longitude' => 151.2333],
                    'Rushcutters Bay' => ['latitude' => -33.8667, 'longitude' => 151.2417],
                    'Double Bay' => ['latitude' => -33.8750, 'longitude' => 151.2417],
                    'Edgecliff' => ['latitude' => -33.8750, 'longitude' => 151.2500],
                    'Kingsford' => ['latitude' => -33.9167, 'longitude' => 151.2333],
                    'Randwick' => ['latitude' => -33.9167, 'longitude' => 151.2500],
                    'Coogee' => ['latitude' => -33.9167, 'longitude' => 151.2667],
                    'Clovelly' => ['latitude' => -33.9167, 'longitude' => 151.2833],
                    'Bronte' => ['latitude' => -33.9167, 'longitude' => 151.3000],
                    'Waverley' => ['latitude' => -33.9000, 'longitude' => 151.2667],
                    'Bondi Junction' => ['latitude' => -33.8917, 'longitude' => 151.2667],
                    'Paddington' => ['latitude' => -33.8833, 'longitude' => 151.2333],
                    'Woollahra' => ['latitude' => -33.8833, 'longitude' => 151.2500],
                    'Bellevue Hill' => ['latitude' => -33.8750, 'longitude' => 151.2667],
                    'Vaucluse' => ['latitude' => -33.8667, 'longitude' => 151.2667],
                    'Rose Bay' => ['latitude' => -33.8667, 'longitude' => 151.2750],
                    'Dover Heights' => ['latitude' => -33.8667, 'longitude' => 151.2833],
                    'Watsons Bay' => ['latitude' => -33.8500, 'longitude' => 151.2833],
                    'South Head' => ['latitude' => -33.8333, 'longitude' => 151.2833],
                    'North Sydney' => ['latitude' => -33.8333, 'longitude' => 151.2083],
                    'Crows Nest' => ['latitude' => -33.8333, 'longitude' => 151.2000],
                    'Neutral Bay' => ['latitude' => -33.8333, 'longitude' => 151.2167],
                    'Mosman' => ['latitude' => -33.8333, 'longitude' => 151.2333],
                    'Cremorne' => ['latitude' => -33.8333, 'longitude' => 151.2250],
                    'Cremorne Point' => ['latitude' => -33.8333, 'longitude' => 151.2417],
                    'Kirribilli' => ['latitude' => -33.8500, 'longitude' => 151.2167],
                    'Milsons Point' => ['latitude' => -33.8500, 'longitude' => 151.2083],
                    'McMahons Point' => ['latitude' => -33.8500, 'longitude' => 151.2000],
                    'Lavender Bay' => ['latitude' => -33.8500, 'longitude' => 151.1917],
                    'Waverton' => ['latitude' => -33.8500, 'longitude' => 151.1833],
                    'Wollstonecraft' => ['latitude' => -33.8500, 'longitude' => 151.1750],
                    'St Leonards' => ['latitude' => -33.8500, 'longitude' => 151.1667],
                    'Artarmon' => ['latitude' => -33.8500, 'longitude' => 151.1583],
                    'Willoughby' => ['latitude' => -33.8500, 'longitude' => 151.1500],
                    'Castlecrag' => ['latitude' => -33.8500, 'longitude' => 151.1417],
                    'Castle Cove' => ['latitude' => -33.8500, 'longitude' => 151.1333],
                    'Middle Cove' => ['latitude' => -33.8500, 'longitude' => 151.1250],
                    'Castle Cove' => ['latitude' => -33.8500, 'longitude' => 151.1167],
                    'Roseville' => ['latitude' => -33.8500, 'longitude' => 151.1083],
                    'Lindfield' => ['latitude' => -33.8500, 'longitude' => 151.1000],
                    'Killara' => ['latitude' => -33.8500, 'longitude' => 151.0917],
                    'Gordon' => ['latitude' => -33.8500, 'longitude' => 151.0833],
                    'Pymble' => ['latitude' => -33.8500, 'longitude' => 151.0750],
                    'Turramurra' => ['latitude' => -33.8500, 'longitude' => 151.0667],
                    'Wahroonga' => ['latitude' => -33.8500, 'longitude' => 151.0583],
                    'Warrawee' => ['latitude' => -33.8500, 'longitude' => 151.0500],
                    'Warrimoo' => ['latitude' => -33.8500, 'longitude' => 151.0417],
                    'Winmalee' => ['latitude' => -33.8500, 'longitude' => 151.0333],
                    'Springwood' => ['latitude' => -33.8500, 'longitude' => 151.0250],
                    'Faulconbridge' => ['latitude' => -33.8500, 'longitude' => 151.0167],
                    'Glenbrook' => ['latitude' => -33.8500, 'longitude' => 151.0083],
                    'Blaxland' => ['latitude' => -33.8500, 'longitude' => 151.0000],
                    'Emu Plains' => ['latitude' => -33.8500, 'longitude' => 150.9917],
                    'Penrith' => ['latitude' => -33.7500, 'longitude' => 150.7000],
                    'St Marys' => ['latitude' => -33.7667, 'longitude' => 150.7750],
                    'Mount Druitt' => ['latitude' => -33.7667, 'longitude' => 150.8167],
                    'Rooty Hill' => ['latitude' => -33.7667, 'longitude' => 150.8333],
                    'Doonside' => ['latitude' => -33.7667, 'longitude' => 150.8500],
                    'Blacktown' => ['latitude' => -33.7686, 'longitude' => 150.9108],
                    'Seven Hills' => ['latitude' => -33.7667, 'longitude' => 150.9333],
                    'Toongabbie' => ['latitude' => -33.7667, 'longitude' => 150.9500],
                    'Wentworthville' => ['latitude' => -33.7667, 'longitude' => 150.9667],
                    'Westmead' => ['latitude' => -33.7667, 'longitude' => 150.9833],
                    'Parramatta' => ['latitude' => -33.8148, 'longitude' => 151.0019],
                    'Harris Park' => ['latitude' => -33.8167, 'longitude' => 151.0167],
                    'Rosehill' => ['latitude' => -33.8167, 'longitude' => 151.0333],
                    'Clyde' => ['latitude' => -33.8167, 'longitude' => 151.0500],
                    'Granville' => ['latitude' => -33.8167, 'longitude' => 151.0667],
                    'Merrylands' => ['latitude' => -33.8167, 'longitude' => 151.0833],
                    'Guildford' => ['latitude' => -33.8167, 'longitude' => 151.1000],
                    'Yennora' => ['latitude' => -33.8167, 'longitude' => 151.1167],
                    'Smithfield' => ['latitude' => -33.8167, 'longitude' => 151.1333],
                    'Fairfield' => ['latitude' => -33.8667, 'longitude' => 150.9500],
                    'Cabramatta' => ['latitude' => -33.9000, 'longitude' => 150.9333],
                    'Canley Vale' => ['latitude' => -33.9000, 'longitude' => 150.9500],
                    'Canley Heights' => ['latitude' => -33.9000, 'longitude' => 150.9667],
                    'Lansvale' => ['latitude' => -33.9000, 'longitude' => 150.9833],
                    'Carramar' => ['latitude' => -33.9000, 'longitude' => 151.0000],
                    'Villawood' => ['latitude' => -33.9000, 'longitude' => 151.0167],
                    'Bossley Park' => ['latitude' => -33.9000, 'longitude' => 151.0333],
                    'Abbotsbury' => ['latitude' => -33.9000, 'longitude' => 151.0500],
                    'Edensor Park' => ['latitude' => -33.9000, 'longitude' => 151.0667],
                    'Bonnyrigg' => ['latitude' => -33.9000, 'longitude' => 151.0833],
                    'Greenfield Park' => ['latitude' => -33.9000, 'longitude' => 151.1000],
                    'Prairiewood' => ['latitude' => -33.9000, 'longitude' => 151.1167],
                    'Wetherill Park' => ['latitude' => -33.9000, 'longitude' => 151.1333],
                    'Horsley Park' => ['latitude' => -33.9000, 'longitude' => 151.1500],
                    'Cecil Park' => ['latitude' => -33.9000, 'longitude' => 151.1667],
                    'Kemps Creek' => ['latitude' => -33.9000, 'longitude' => 151.1833],
                    'Badgerys Creek' => ['latitude' => -33.9000, 'longitude' => 151.2000],
                    'Luddenham' => ['latitude' => -33.9000, 'longitude' => 151.2167],
                    'Wallacia' => ['latitude' => -33.9000, 'longitude' => 151.2333],
                    'Silverdale' => ['latitude' => -33.9000, 'longitude' => 151.2500],
                    'Warwick Farm' => ['latitude' => -33.9000, 'longitude' => 151.2667],
                    'Liverpool' => ['latitude' => -33.9249, 'longitude' => 150.9259],
                    'Casula' => ['latitude' => -33.9500, 'longitude' => 150.9167],
                    'Hammondville' => ['latitude' => -33.9500, 'longitude' => 150.9333],
                    'Voyager Point' => ['latitude' => -33.9500, 'longitude' => 150.9500],
                    'Sandy Point' => ['latitude' => -33.9500, 'longitude' => 150.9667],
                    'Holsworthy' => ['latitude' => -33.9500, 'longitude' => 150.9833],
                    'Wattle Grove' => ['latitude' => -33.9500, 'longitude' => 151.0000],
                    'Hammondville' => ['latitude' => -33.9500, 'longitude' => 151.0167],
                    'Moorebank' => ['latitude' => -33.9500, 'longitude' => 151.0333],
                    'Chipping Norton' => ['latitude' => -33.9500, 'longitude' => 151.0500],
                    'Milperra' => ['latitude' => -33.9500, 'longitude' => 151.0667],
                    'Revesby' => ['latitude' => -33.9500, 'longitude' => 151.0833],
                    'Padstow' => ['latitude' => -33.9500, 'longitude' => 151.1000],
                    'Riverwood' => ['latitude' => -33.9500, 'longitude' => 151.1167],
                    'Narwee' => ['latitude' => -33.9500, 'longitude' => 151.1333],
                    'Beverly Hills' => ['latitude' => -33.9500, 'longitude' => 151.1500],
                    'Kingsgrove' => ['latitude' => -33.9500, 'longitude' => 151.1667],
                    'Bexley' => ['latitude' => -33.9500, 'longitude' => 151.1833],
                    'Bexley North' => ['latitude' => -33.9500, 'longitude' => 151.2000],
                    'Bardwell Park' => ['latitude' => -33.9500, 'longitude' => 151.2167],
                    'Turrella' => ['latitude' => -33.9500, 'longitude' => 151.2333],
                    'Wolli Creek' => ['latitude' => -33.9500, 'longitude' => 151.2500],
                    'Arncliffe' => ['latitude' => -33.9500, 'longitude' => 151.2667],
                    'Banksia' => ['latitude' => -33.9500, 'longitude' => 151.2833],
                    'Rockdale' => ['latitude' => -33.9500, 'longitude' => 151.3000],
                    'Kogarah' => ['latitude' => -33.9500, 'longitude' => 151.3167],
                    'Carlton' => ['latitude' => -33.9500, 'longitude' => 151.3333],
                    'Allawah' => ['latitude' => -33.9500, 'longitude' => 151.3500],
                    'Hurstville' => ['latitude' => -33.9667, 'longitude' => 151.1000],
                    'Penshurst' => ['latitude' => -33.9667, 'longitude' => 151.1167],
                    'Mortdale' => ['latitude' => -33.9667, 'longitude' => 151.1333],
                    'Oatley' => ['latitude' => -33.9667, 'longitude' => 151.1500],
                    'Kangaroo Point' => ['latitude' => -33.9667, 'longitude' => 151.1667],
                    'Como' => ['latitude' => -33.9667, 'longitude' => 151.1833],
                    'Jannali' => ['latitude' => -33.9667, 'longitude' => 151.2000],
                    'Sutherland' => ['latitude' => -33.9667, 'longitude' => 151.2167],
                    'Kirrawee' => ['latitude' => -33.9667, 'longitude' => 151.2333],
                    'Gymea' => ['latitude' => -33.9667, 'longitude' => 151.2500],
                    'Miranda' => ['latitude' => -33.9667, 'longitude' => 151.2667],
                    'Caringbah' => ['latitude' => -33.9667, 'longitude' => 151.2833],
                    'Cronulla' => ['latitude' => -33.9667, 'longitude' => 151.3000],
                    'Kurnell' => ['latitude' => -33.9667, 'longitude' => 151.3167],
                    'Taren Point' => ['latitude' => -33.9667, 'longitude' => 151.3333],
                    'Sylvania' => ['latitude' => -33.9667, 'longitude' => 151.3500],
                    'Sylvania Waters' => ['latitude' => -33.9667, 'longitude' => 151.3667],
                    'Blakehurst' => ['latitude' => -33.9667, 'longitude' => 151.3833],
                    'Kyle Bay' => ['latitude' => -33.9667, 'longitude' => 151.4000],
                    'Connells Point' => ['latitude' => -33.9667, 'longitude' => 151.4167],
                    'South Hurstville' => ['latitude' => -33.9667, 'longitude' => 151.4333],
                    'Peakhurst' => ['latitude' => -33.9667, 'longitude' => 151.4500],
                    'Peakhurst Heights' => ['latitude' => -33.9667, 'longitude' => 151.4667],
                    'Lugarno' => ['latitude' => -33.9667, 'longitude' => 151.4833],
                    'Riverwood' => ['latitude' => -33.9500, 'longitude' => 151.1167],
                    'Narwee' => ['latitude' => -33.9500, 'longitude' => 151.1333],
                    'Beverly Hills' => ['latitude' => -33.9500, 'longitude' => 151.1500],
                    'Kingsgrove' => ['latitude' => -33.9500, 'longitude' => 151.1667],
                    'Bexley' => ['latitude' => -33.9500, 'longitude' => 151.1833],
                    'Bexley North' => ['latitude' => -33.9500, 'longitude' => 151.2000],
                    'Bardwell Park' => ['latitude' => -33.9500, 'longitude' => 151.2167],
                    'Turrella' => ['latitude' => -33.9500, 'longitude' => 151.2333],
                    'Wolli Creek' => ['latitude' => -33.9500, 'longitude' => 151.2500],
                    'Arncliffe' => ['latitude' => -33.9500, 'longitude' => 151.2667],
                    'Banksia' => ['latitude' => -33.9500, 'longitude' => 151.2833],
                    'Rockdale' => ['latitude' => -33.9500, 'longitude' => 151.3000],
                    'Kogarah' => ['latitude' => -33.9500, 'longitude' => 151.3167],
                    'Carlton' => ['latitude' => -33.9500, 'longitude' => 151.3333],
                    'Allawah' => ['latitude' => -33.9500, 'longitude' => 151.3500],
                    'Hurstville' => ['latitude' => -33.9667, 'longitude' => 151.1000],
                    'Penshurst' => ['latitude' => -33.9667, 'longitude' => 151.1167],
                    'Mortdale' => ['latitude' => -33.9667, 'longitude' => 151.1333],
                    'Oatley' => ['latitude' => -33.9667, 'longitude' => 151.1500],
                    'Kangaroo Point' => ['latitude' => -33.9667, 'longitude' => 151.1667],
                    'Como' => ['latitude' => -33.9667, 'longitude' => 151.1833],
                    'Jannali' => ['latitude' => -33.9667, 'longitude' => 151.2000],
                    'Sutherland' => ['latitude' => -33.9667, 'longitude' => 151.2167],
                    'Kirrawee' => ['latitude' => -33.9667, 'longitude' => 151.2333],
                    'Gymea' => ['latitude' => -33.9667, 'longitude' => 151.2500],
                    'Miranda' => ['latitude' => -33.9667, 'longitude' => 151.2667],
                    'Caringbah' => ['latitude' => -33.9667, 'longitude' => 151.2833],
                    'Cronulla' => ['latitude' => -33.9667, 'longitude' => 151.3000],
                    'Kurnell' => ['latitude' => -33.9667, 'longitude' => 151.3167],
                    'Taren Point' => ['latitude' => -33.9667, 'longitude' => 151.3333],
                    'Sylvania' => ['latitude' => -33.9667, 'longitude' => 151.3500],
                    'Sylvania Waters' => ['latitude' => -33.9667, 'longitude' => 151.3667],
                    'Blakehurst' => ['latitude' => -33.9667, 'longitude' => 151.3833],
                    'Kyle Bay' => ['latitude' => -33.9667, 'longitude' => 151.4000],
                    'Connells Point' => ['latitude' => -33.9667, 'longitude' => 151.4167],
                    'South Hurstville' => ['latitude' => -33.9667, 'longitude' => 151.4333],
                    'Peakhurst' => ['latitude' => -33.9667, 'longitude' => 151.4500],
                    'Peakhurst Heights' => ['latitude' => -33.9667, 'longitude' => 151.4667],
                    'Lugarno' => ['latitude' => -33.9667, 'longitude' => 151.4833]
                ];
                
                // Get coordinates for the suburb, or use state-based fallback
                if (!empty($suburb) && isset($mockCoordinates[$suburb])) {
                    $latitude = $mockCoordinates[$suburb]['latitude'];
                    $longitude = $mockCoordinates[$suburb]['longitude'];
                } elseif (!empty($state)) {
                    // Fallback to state capital coordinates
                    $stateCapitals = [
                        'NSW' => ['latitude' => -33.8688, 'longitude' => 151.2093], // Sydney
                        'VIC' => ['latitude' => -37.8136, 'longitude' => 144.9631], // Melbourne
                        'QLD' => ['latitude' => -27.4698, 'longitude' => 153.0251], // Brisbane
                        'WA' => ['latitude' => -31.9505, 'longitude' => 115.8605], // Perth
                        'SA' => ['latitude' => -34.9285, 'longitude' => 138.6007], // Adelaide
                        'TAS' => ['latitude' => -42.8821, 'longitude' => 147.3272], // Hobart
                        'NT' => ['latitude' => -12.4634, 'longitude' => 130.8456], // Darwin
                        'ACT' => ['latitude' => -35.2809, 'longitude' => 149.1300]  // Canberra
                    ];
                    
                    if (isset($stateCapitals[$state])) {
                        $latitude = $stateCapitals[$state]['latitude'];
                        $longitude = $stateCapitals[$state]['longitude'];
                    } else {
                        // Final fallback to Australia center
                        $latitude = -25.2744;
                        $longitude = 133.7751;
                    }
                } else {
                    // No suburb or state - use Australia center
                    $latitude = -25.2744;
                    $longitude = 133.7751;
                }
                
                // Calculate demographics for this location
                $disabilities = $participants->pluck('primary_disability')->filter()->values();
                $genders = $participants->pluck('gender_identity')->filter()->values();
                
                return (object) [
                    'location' => $locationKey,
                    'suburb' => $suburb,
                    'state' => $state,
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                    'participant_count' => $participants->count(),
                    'disabilities' => $disabilities,
                    'genders' => $genders
                ];
            })
            ->values();

        return [
            'topSuburbs' => $topSuburbs,
            'topStates' => $topStates,
            'topDisabilities' => $topDisabilities,
            'allParticipants' => $allParticipants
        ];
    }

    public function editProfile()
    {
        $user = Auth::user();
        $subscriptionStatus = \App\Services\SubscriptionService::getSubscriptionStatus();
        $provider = $user->provider;
        
        if (!$provider) {
            // Handle case where provider profile doesn't exist (shouldn't happen after registration)
            abort(404);
        }
        
        return view('provider.profile', compact('user', 'provider', 'subscriptionStatus'));
    }

    public function updateProfile(Request $request)
    {
        $subscriptionStatus = \App\Services\SubscriptionService::getSubscriptionStatus();
        $provider = Auth::user()->provider;
        if (!$provider) {
            abort(404);
        }

        // Validate the request
        $request->validate([
            'business_name' => 'required|string|max:255',
            'contact_person' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:10',
            'postal_code' => 'required|string|max:10',
            'website' => 'nullable|url|max:255',
            'description' => 'nullable|string|max:1000',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120', // 5MB max
        ]);

        try {
            // Handle logo upload
            if ($request->hasFile('logo')) {
                // Delete old logo if exists
                if ($provider->logo && \Storage::disk('public')->exists($provider->logo)) {
                    \Storage::disk('public')->delete($provider->logo);
                }

                // Store new logo
                $logoPath = $request->file('logo')->store('provider-logos', 'public');
                $provider->logo = $logoPath;
            }

            // Update provider information
            $provider->update([
                'business_name' => $request->business_name,
                'contact_person' => $request->contact_person,
                'email' => $request->email,
                'phone' => $request->phone,
                'address' => $request->address,
                'city' => $request->city,
                'state' => $request->state,
                'postal_code' => $request->postal_code,
                'website' => $request->website,
                'description' => $request->description,
            ]);

            return redirect()->route('provider.profile.edit')->with('success', 'Profile updated successfully!')
                ->with('subscriptionStatus', $subscriptionStatus);

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'An error occurred while updating your profile. Please try again.');
        }
    }

    public function listParticipants(Request $request)
    {
        $subscriptionStatus = \App\Services\SubscriptionService::getSubscriptionStatus();
        $provider = Auth::user();

        // Start with the participants managed by the current coordinator
        $query = $provider->participantsAdded();

        // --- Search and Filter Logic ---

        // Search by name (first, last, or middle) or specific disability
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', '%' . $search . '%')
                    ->orWhere('last_name', 'like', '%' . $search . '%')
                    ->orWhere('middle_name', 'like', '%' . $search . '%')
                    ->orWhere('primary_disability', 'like', '%' . $search . '%')
                    ->orWhere('secondary_disability', 'like', '%' . $search . '%');
            });
        }

        // Filter by Primary Disability
        if ($request->filled('primary_disability')) {
            $query->where('primary_disability', $request->input('primary_disability'));
        }

        // Filter by State
        if ($request->filled('state')) {
            $query->where('state', $request->input('state'));
        }

        // Filter by Suburb (only if state is also selected)
        if ($request->filled('suburb') && $request->filled('state')) {
            $query->where('suburb', $request->input('suburb'));
        }

        // --- End Search and Filter Logic ---

        $participants = $query->paginate(10);

        // For the filter dropdowns:
        $primaryDisabilityTypes = Participant::distinct()->pluck('primary_disability')->filter()->sort()->toArray();

        $suburbsForFilter = [];
        if ($request->filled('state')) {
            $suburbsForFilter = DB::table('participants')
                ->where('state', $request->input('state'))
                ->distinct()
                ->orderBy('suburb')
                ->pluck('suburb')
                ->toArray();
        }

        // Get subscription status and participant limit information
        $subscriptionStatus = \App\Services\SubscriptionService::getSubscriptionStatus();
        $currentParticipantCount = $provider->participantsAdded()->count();
        $canAddParticipant = \App\Services\SubscriptionService::canAddParticipant($currentParticipantCount);
        $participantLimit = \App\Services\SubscriptionService::getParticipantLimit();
        
        // Get participant deletion eligibility
        $deletionEligibility = \App\Services\SubscriptionService::canDeleteParticipant();

        return view('company.participants.index', compact(
            'participants', 
            'primaryDisabilityTypes', 
            'suburbsForFilter', 
            'subscriptionStatus', 
            'provider', 
            'canAddParticipant', 
            'participantLimit', 
            'currentParticipantCount',
            'deletionEligibility'
        ));
    }

    // public function updateProfile(Request $request)
    // {
    //     $user = Auth::user();
    //     $provider = $user->provider;

    //     // Define validation rules for provider profile updates
    //     $request->validate([
    //         'organisation_name' => ['required', 'string', 'max:255'],
    //         'abn' => ['required', 'string', 'digits:11', 'unique:providers,abn,' . $provider->id], // Exclude current provider's ABN
    //         'main_contact_name' => ['required', 'string', 'max:255'],
    //         'phone_number' => ['nullable', 'string', 'max:20'],
    //         'email_address' => ['nullable', 'email', 'max:255'],
    //         'office_address' => ['nullable', 'string', 'max:255'],
    //         'office_suburb' => ['nullable', 'string', 'max:255'],
    //         'office_state' => ['nullable', 'string', 'max:255'],
    //         'office_post_code' => ['nullable', 'string', 'max:10'],
    //         // Add validation for other fields like plan, provider_logo if they'll be editable here
    //     ]);

    //     // Update the user's name if it's tied to the contact person
    //     $user->update([
    //         'first_name' => explode(' ', $request->main_contact_name)[0] ?? $user->first_name,
    //         'last_name' => explode(' ', $request->main_contact_name, 2)[1] ?? $user->last_name,
    //     ]);

    //     // Update the provider's details
    //     $provider->update([
    //         'organisation_name' => $request->organisation_name,
    //         'abn' => $request->abn,
    //         'main_contact_name' => $request->main_contact_name,
    //         'phone_number' => $request->phone_number,
    //         'email_address' => $request->email_address,
    //         'office_address' => $request->office_address,
    //         'office_suburb' => $request->office_suburb,
    //         'office_state' => $request->office_state,
    //         'office_post_code' => $request->office_post_code,
    //     ]);

    //     return redirect()->route('provider.dashboard')->with('status', 'Provider profile updated successfully.');
    // }

    public function billing()
    {
        // Load the authenticated user's associated provider profile
        $provider = Auth::user()->provider;
        
        if (!$provider) {
            return redirect()->route('provider.create')->with('error', 'Please complete your provider profile first.');
        }

        // Get subscription status
        $subscriptionStatus = \App\Services\SubscriptionService::getSubscriptionStatus();

        // Get payment history
        $payments = Auth::user()->payments()
            ->with('subscription')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('provider.billing', compact('subscriptionStatus', 'provider', 'payments'));
    }

    /**
     * Support Center - View user's tickets
     */
    public function supportCenter(Request $request)
    {
        $subscriptionStatus = \App\Services\SubscriptionService::getSubscriptionStatus();
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
        
        return view('company.support-center.index', compact(
            'tickets', 
            'totalTickets', 
            'openTickets', 
            'inProgressTickets', 
            'resolvedTickets',
            'categories',
            'subscriptionStatus'
        ));
    }

    /**
     * Create a new support ticket
     */
    public function createTicket(Request $request)
    {
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
        
        return redirect()->route('provider.support-center.index')
            ->with('success', "Ticket {$ticket->ticket_number} created successfully!");
    }

    /**
     * View individual ticket
     */
    public function viewTicket(SupportTicket $ticket)
    {
        $subscriptionStatus = \App\Services\SubscriptionService::getSubscriptionStatus();
        // Ensure user can only view their own tickets
        if ($ticket->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to ticket.');
        }
        
        $ticket->load(['category', 'assignedAdmin', 'comments.user']);
        $categories = SupportCategory::active()->ordered()->get();
        
        return view('company.support-center.view', compact('ticket', 'categories', 'subscriptionStatus'));
    }

    /**
     * Add comment to ticket
     */
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
        
        return redirect()->route('provider.support-center.view', $ticket)
            ->with('success', 'Comment added successfully!');
    }

    /**
     * Show match requests page for providers
     */
    public function matchRequests()
    {
        $subscriptionStatus = \App\Services\SubscriptionService::getSubscriptionStatus();
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

        return view('company.match-requests', compact('pendingRequests', 'sentRequests', 'acceptedRequests', 'pendingCount', 'subscriptionStatus'));
    }
}