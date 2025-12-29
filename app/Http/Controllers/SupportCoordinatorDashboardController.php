<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Participant;
use App\Models\SupportTicket;
use App\Models\SupportCategory;
use App\Models\SupportTicketComment;
use App\Models\MatchRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Storage;

class SupportCoordinatorDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:coordinator']);
    }

    public function index()
    {
        $coordinator = Auth::user();

        // Quick Links Data (Counts)
        $totalParticipants = $coordinator->participantsAdded()->count();

        // # of no current accommodation (where current living situation is not SIL or SDA accommodation)
        $noCurrentSilSdaAccommodation = $coordinator->participantsAdded()
            ->where('current_living_situation', '!=', 'SIL or SDA accommodation')
            ->count();
        
        $participantsLookingForAccommodation = $coordinator->participantsAdded()->where('move_in_availability', '!=', 'Just exploring options')->count(); // Using schema field for looking for accommodation

        // Chart Data: Participants Per State (for current coordinator)
        $participantsPerState = $coordinator->participantsAdded()
            ->select('state', DB::raw('count(*) as total'))
            ->whereNotNull('state')
            ->where('state', '!=', '')
            ->groupBy('state')
            ->orderBy('total', 'desc')
            ->get();

        // Chart Data: Participants Per Suburb (Top 10, for current coordinator)
        $participantsPerSuburb = $coordinator->participantsAdded()
            ->select('suburb', DB::raw('count(*) as total'))
            ->whereNotNull('suburb')
            ->where('suburb', '!=', '')
            ->groupBy('suburb')
            ->orderBy('total', 'desc')
            ->limit(10)
            ->get();

        // Chart Data: Participants Per Primary Disability (for current coordinator)
        $participantsPerPrimaryDisability = $coordinator->participantsAdded()
            ->select('primary_disability', DB::raw('count(*) as total'))
            ->whereNotNull('primary_disability')
            ->where('primary_disability', '!=', '')
            ->groupBy('primary_disability')
            ->orderBy('total', 'desc')
            ->limit(10)
            ->get();

        // Chart Data: Participants Per Age Range (for current coordinator) - formatted for charts
        $ageRangeData = $coordinator->participantsAdded()->select(
            DB::raw("SUM(CASE WHEN date_of_birth IS NOT NULL AND TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) BETWEEN 18 AND 25 THEN 1 ELSE 0 END) as age_18_25"),
            DB::raw("SUM(CASE WHEN date_of_birth IS NOT NULL AND TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) BETWEEN 26 AND 35 THEN 1 ELSE 0 END) as age_26_35"),
            DB::raw("SUM(CASE WHEN date_of_birth IS NOT NULL AND TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) BETWEEN 36 AND 50 THEN 1 ELSE 0 END) as age_36_50"),
            DB::raw("SUM(CASE WHEN date_of_birth IS NOT NULL AND TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) > 50 THEN 1 ELSE 0 END) as age_51_plus"),
            DB::raw("SUM(CASE WHEN date_of_birth IS NULL THEN 1 ELSE 0 END) as age_unknown")
        )->first();

        // Format age range data for charts
        $participantsPerAgeRange = collect([
            ['age_range' => '18-25', 'total' => $ageRangeData->age_18_25 ?? 0],
            ['age_range' => '26-35', 'total' => $ageRangeData->age_26_35 ?? 0],
            ['age_range' => '36-50', 'total' => $ageRangeData->age_36_50 ?? 0],
            ['age_range' => '51+', 'total' => $ageRangeData->age_51_plus ?? 0],
            ['age_range' => 'Unknown', 'total' => $ageRangeData->age_unknown ?? 0]
        ])->filter(function($item) {
            return $item['total'] > 0;
        });

        // Chart Data: Participants Per Gender (for current coordinator)
        $participantsPerGender = $coordinator->participantsAdded()
            ->select('gender_identity', DB::raw('count(*) as total'))
            ->whereNotNull('gender_identity')
            ->where('gender_identity', '!=', '')
            ->groupBy('gender_identity')
            ->orderBy('total', 'desc')
            ->get();

        // Gender distribution data (replacing accommodation status)
        $genderDistributionData = $coordinator->participantsAdded()
            ->selectRaw('gender_identity, COUNT(*) as count')
            ->whereNotNull('gender_identity')
            ->where('gender_identity', '!=', '')
            ->groupBy('gender_identity')
            ->orderBy('gender_identity')
            ->get()
            ->map(function($item) {
                return [
                    'gender' => trim($item->gender_identity),
                    'count' => $item->count
                ];
            })
            ->filter(function($item) {
                return $item['count'] > 0;
            });

        // Add coordinates for suburbs (mock data - in real app, you'd have a suburbs table with coordinates)
        $participantsPerSuburb = $participantsPerSuburb->map(function($item) {
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

        // Recent Activities - Real data from database
        $recentActivities = collect();
        
        // Get recent participants (last 7 days)
        $recentParticipants = $coordinator->participantsAdded()->where('created_at', '>=', now()->subDays(7))->get();
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
        
        // Get recent participant updates (last 7 days)
        $recentParticipantUpdates = $coordinator->participantsAdded()->where('updated_at', '>=', now()->subDays(7))
            ->where('updated_at', '>', DB::raw('created_at'))->get();
        foreach ($recentParticipantUpdates as $participant) {
            if ($participant->updated_at) {
                $recentActivities->push([
                    'description' => 'Participant profile updated: ' . $participant->first_name . ' ' . $participant->last_name,
                    'time' => $participant->updated_at->diffForHumans(),
                    'type' => 'participant_update',
                    'created_at' => $participant->updated_at
                ]);
            }
        }
        
        // Get recent messages (if you have a messages/conversations system)
        // This would require checking if you have a messages table
        // For now, we'll add a placeholder for future implementation
        /*
        $recentMessages = \App\Models\Message::where('support_coordinator_id', $coordinator->id)
            ->where('created_at', '>=', now()->subDays(7))
            ->get();
        foreach ($recentMessages as $message) {
            $recentActivities->push([
                'description' => 'New message received from participant',
                'time' => $message->created_at->diffForHumans(),
                'type' => 'message',
                'created_at' => $message->created_at
            ]);
        }
        */
        
        // Sort by creation time and take the 5 most recent
        $recentActivities = $recentActivities->sortByDesc('created_at')->take(5);

        // Accepted Match Request Statistics
        $acceptedMatchRequests = MatchRequest::where('status', 'accepted')
            ->where(function($query) use ($coordinator) {
                $query->where('sender_user_id', $coordinator->id)
                      ->orWhere('receiver_user_id', $coordinator->id);
            })
            ->count();

        // System-wide Analytics (all participants in the system)
        $systemWideAnalytics = $this->getSystemWideAnalytics();

        return view('supcoor.dashboard-content', compact(
            'totalParticipants',
            'noCurrentSilSdaAccommodation',
            'participantsLookingForAccommodation',
            'participantsPerState',
            'participantsPerSuburb',
            'participantsPerPrimaryDisability',
            'participantsPerAgeRange',
            'participantsPerGender',
            'genderDistributionData',
            'recentActivities',
            'acceptedMatchRequests',
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
                    'St Marys' => ['latitude' => -33.7667, 'longitude' => 150.7750],
                    'Mount Druitt' => ['latitude' => -33.7667, 'longitude' => 150.8167],
                    'Rooty Hill' => ['latitude' => -33.7667, 'longitude' => 150.8333],
                    'Doonside' => ['latitude' => -33.7667, 'longitude' => 150.8500],
                    'Seven Hills' => ['latitude' => -33.7667, 'longitude' => 150.9333],
                    'Toongabbie' => ['latitude' => -33.7667, 'longitude' => 150.9500],
                    'Wentworthville' => ['latitude' => -33.7667, 'longitude' => 150.9667],
                    'Westmead' => ['latitude' => -33.7667, 'longitude' => 150.9833],
                    'Harris Park' => ['latitude' => -33.8167, 'longitude' => 151.0167],
                    'Rosehill' => ['latitude' => -33.8167, 'longitude' => 151.0333],
                    'Clyde' => ['latitude' => -33.8167, 'longitude' => 151.0500],
                    'Granville' => ['latitude' => -33.8167, 'longitude' => 151.0667],
                    'Merrylands' => ['latitude' => -33.8167, 'longitude' => 151.0833],
                    'Guildford' => ['latitude' => -33.8167, 'longitude' => 151.1000],
                    'Yennora' => ['latitude' => -33.8167, 'longitude' => 151.1167],
                    'Smithfield' => ['latitude' => -33.8167, 'longitude' => 151.1333],
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
                    'Casula' => ['latitude' => -33.9500, 'longitude' => 150.9167],
                    'Hammondville' => ['latitude' => -33.9500, 'longitude' => 150.9333],
                    'Voyager Point' => ['latitude' => -33.9500, 'longitude' => 150.9500],
                    'Sandy Point' => ['latitude' => -33.9500, 'longitude' => 150.9667],
                    'Holsworthy' => ['latitude' => -33.9500, 'longitude' => 150.9833],
                    'Wattle Grove' => ['latitude' => -33.9500, 'longitude' => 151.0000],
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

    /**
     * Display the support coordinator dashboard with a list of participants.
     */
    public function listParticipants(Request $request)
    {
        $coordinator = Auth::user();

        // Start with the participants managed by the current coordinator
        $query = $coordinator->participantsAdded();

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

        return view('supcoor.participants.index', compact('participants', 'primaryDisabilityTypes', 'suburbsForFilter'));
    }

    public function viewUnassignedParticipants(Request $request)
    {
        $query = Participant::withoutSupportCoordinator();

        // Apply filters
        if ($request->filled('state')) {
            $query->where('state', $request->state);
        }

        if ($request->filled('suburb')) {
            $query->where('suburb', $request->suburb);
        }

        if ($request->filled('current_living_situation')) {
            $query->where('current_living_situation', $request->current_living_situation);
        }

        if ($request->filled('primary_disability')) {
            $query->where('primary_disability', $request->primary_disability);
        }

        // Search by Participant Code Name, disability fields, or current living situation
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('participant_code_name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('primary_disability', 'like', '%' . $searchTerm . '%')
                  ->orWhere('secondary_disability', 'like', '%' . $searchTerm . '%') // Assuming secondary_disability exists
                  ->orWhere('current_living_situation', 'like', '%' . $searchTerm . '%'); // Added current living situation to search
            });
        }

        $participants = $query->paginate(10);

        // --- Get unique filter options from ALL unassigned participants for dropdowns ---
        // These are distinct values from participants that are currently unassigned.

        $states = Participant::withoutSupportCoordinator()
            ->distinct()
            ->pluck('state')
            ->filter() // Remove any null or empty string states
            ->sort()
            ->values() // Re-index the array
            ->toArray();

        $suburbs = [];
        if ($request->filled('state')) {
            $suburbs = Participant::withoutSupportCoordinator()
                ->where('state', $request->input('state'))
                ->distinct()
                ->pluck('suburb')
                ->filter() // Remove any null or empty string suburbs
                ->sort()
                ->values() // Re-index the array
                ->toArray();
        }
        // If no state is selected, we don't load all suburbs to avoid a massive list.
        // The Blade's Alpine.js `loadSuburbsForFilter` function handles loading all for the selected state.
        // The initial $suburbs passed here is primarily for when a state filter IS applied and page reloads.

        // Predefined list for current living situation types for the dropdown
        // It's good to have a fixed list for this type of categorisation.
        $currentLivingSituations = [
            'SIL or SDA accommodation',
            'Group home',
            'With family',
            'Living alone',
            'Other'
        ];

        // Dynamically collect ALL unique primary disability types from unassigned participants
        $primaryDisabilityTypes = Participant::withoutSupportCoordinator()
            ->distinct()
            ->pluck('primary_disability')
            ->filter() // Remove any null or empty primary disabilities
            ->sort()
            ->values() // Re-index the array
            ->toArray();

        return view('supcoor.unassigned_participants', [
            'participants' => $participants,
            'states' => $states,
            'suburbs' => $suburbs, // This will be the filtered suburbs if a state is selected, otherwise empty initially for dynamic load
            'currentLivingSituations' => $currentLivingSituations,
            'primaryDisabilityTypes' => $primaryDisabilityTypes,
            'filters' => $request->all(), // Pass all request parameters back to re-populate filters
        ]);
    }

    public function getSuburbsByState($state)
    {
        // Ensure you're only getting suburbs for unassigned participants if that's the intention
        $suburbs = Participant::withoutSupportCoordinator()
            ->where('state', $state)
            ->distinct()
            ->pluck('suburb')
            ->filter()
            ->sort()
            ->values()
            ->toArray();

        return response()->json($suburbs);
    }

    /**
     * Handle sending a message to a participant.
     */
    public function sendMessage(Request $request, Participant $participant)
    {
        $request->validate([
            'message_subject' => 'required|string|max:255',
            'message_body' => 'required|string',
        ]);

        DB::table('messages')->insert([
            'sender_id' => Auth::id(),
            'recipient_id' => $participant->id,
            'subject' => $request->message_subject,
            'body' => $request->message_body,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['message' => 'Message sent successfully!'], 200);
    }


    /**
     * Show the form for adding a new participant.
     */
    public function createParticipant()
    {
        $primaryDisabilities = [
            'Physical Disability', 'Intellectual Disability', 'Sensory Disability',
            'Psychosocial Disability', 'Autism Spectrum Disorder', 'Neurological Disability',
            'Other'
        ];
        $currentLivingSituations = [
            'Private Rental', 'Living with Family', 'Shared Accommodation',
            'Supported Independent Living (SIL)', 'Specialist Disability Accommodation (SDA)',
            'Group Home', 'Boarding House', 'Homeless/Unstable'
        ];
        $genderIdentities = ['Female', 'Male', 'Non-binary', 'Prefer not to say', 'Other'];
        $ndisPlanManagers = ['Self-managed', 'Plan-managed', 'NDIA-managed', 'Not sure'];
        $silFundingStatuses = ['Yes', 'No', 'Not sure'];
        $contactMethods = ['Phone', 'Email', 'Either'];
        $preferredHousemateNumbers = ['1', '2', '3+', 'No preference'];
        $accessibilityNeeds = ['Fully accessible', 'Some modifications required', 'No specific needs'];
        $petPreferences = ['Have pets', 'Can live with pets', 'Do not want to live with pets'];
        $moveInAvailabilities = ['ASAP', 'Within 1–3 months', 'Within 3–6 months', 'Just exploring options'];
        $aboriginalTorresStraitIslanderOptions = ['Yes', 'No', 'Prefer not to say'];
        $medicationAdminHelpOptions = ['Yes', 'No', 'Sometimes'];
        $behaviourSupportPlanStatuses = ['Yes', 'No', 'In development'];
        $preferredContactMatchMethods = ['Phone', 'Email', 'Via support coordinator', 'Other'];

        // Assuming fixed options for other JSON fields for UI selection.
        // In a real application, you might load these from a config or database.
        $pronounOptions = ['She / Her', 'He / Him', 'They / Them', 'Other'];
        $dailyLivingSupportNeedsOptions = [
            'Personal care', 'Medication management', 'Meal preparation',
            'Household tasks', 'Community access', 'Transport', 'Financial management', 'Other'
        ];
        $housematePreferencesOptions = ['Male', 'Female', 'Mixed', 'No preference', 'Other'];
        $goodHomeEnvironmentLooksLikeOptions = [
            'Quiet', 'Social', 'Organized', 'Relaxed', 'Structured', 'Independent', 'Supportive', 'Other'
        ];
        $selfDescriptionOptions = [
            'Quiet', 'Social', 'Independent', 'Needs support', 'Organized', 'Relaxed', 'Active', 'Creative', 'Other'
        ];


        return view('supcoor.participants.create', compact(
            'primaryDisabilities', 'currentLivingSituations', 'genderIdentities',
            'ndisPlanManagers', 'silFundingStatuses', 'contactMethods',
            'preferredHousemateNumbers', 'accessibilityNeeds', 'petPreferences',
            'moveInAvailabilities', 'aboriginalTorresStraitIslanderOptions',
            'medicationAdminHelpOptions', 'behaviourSupportPlanStatuses',
            'preferredContactMatchMethods', 'pronounOptions', 'dailyLivingSupportNeedsOptions',
            'housematePreferencesOptions', 'goodHomeEnvironmentLooksLikeOptions', 'selfDescriptionOptions'
        ));
    }

    /**
     * Store a newly created participant in storage.
     */
    public function storeParticipant(Request $request)
    {
        $coordinator = Auth::user();

        $rules = [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'participant_email' => ['nullable', 'email', 'max:255'],
            'participant_phone' => ['nullable', 'string', 'max:255'],
            'participant_contact_method' => ['nullable', Rule::in(['Phone', 'Email', 'Either'])],
            'is_participant_best_contact' => ['boolean'],

            'date_of_birth' => ['nullable', 'date'],
            'gender_identity' => ['nullable', Rule::in(['Female', 'Male', 'Non-binary', 'Prefer not to say', 'Other'])],
            'gender_identity_other' => ['nullable', 'string', 'max:255'],
            'pronouns' => ['nullable', 'array'],
            'pronouns_other' => ['nullable', 'string', 'max:255'],
            'languages_spoken' => ['nullable', 'array'],
            'aboriginal_torres_strait_islander' => ['nullable', Rule::in(['Yes', 'No', 'Prefer not to say'])],

            'sil_funding_status' => ['nullable', Rule::in(['Yes', 'No', 'Not sure'])],
            'ndis_plan_review_date' => ['nullable', 'date'],
            'ndis_plan_manager' => ['nullable', Rule::in(['Self-managed', 'Plan-managed', 'NDIA-managed', 'Not sure'])],
            'has_support_coordinator' => ['boolean'],

            'daily_living_support_needs' => ['nullable', 'array'],
            'daily_living_support_needs_other' => ['nullable', 'string', 'max:1000'],
            'primary_disability' => ['nullable', 'string', 'max:255'],
            'secondary_disability' => ['nullable', 'string', 'max:255'],
            'estimated_support_hours_sil_level' => ['nullable', 'string', 'max:255'],
            'night_support_type' => ['nullable', Rule::in(['Active overnight', 'Sleepover', 'None'])],
            'uses_assistive_technology_mobility_aids' => ['in:0,1'],
            'assistive_technology_mobility_aids_list' => ['nullable', 'string', 'max:1000'],

            'medical_conditions_relevant' => ['nullable', 'string', 'max:1000'],
            'medication_administration_help' => ['nullable', Rule::in(['Yes', 'No', 'Sometimes'])],
            'behaviour_support_plan_status' => ['nullable', Rule::in(['Yes', 'No', 'In development'])],
            'behaviours_of_concern_housemates' => ['nullable', 'string', 'max:1000'],

            'preferred_sil_locations' => ['nullable', 'array'],
            'housemate_preferences' => ['nullable', 'array'],
            'housemate_preferences_other' => ['nullable', 'string', 'max:1000'],
            'preferred_number_of_housemates' => ['nullable', Rule::in(['1', '2', '3+', 'No preference'])],
            'accessibility_needs_in_home' => ['nullable', Rule::in(['Fully accessible', 'Some modifications required', 'No specific needs'])],
            'accessibility_needs_details' => ['nullable', 'string', 'max:1000'],
            'pets_in_home_preference' => ['nullable', Rule::in(['Have pets', 'Can live with pets', 'Do not want to live with pets'])],
            'own_pet_type' => ['nullable', 'string', 'max:255'],
            'good_home_environment_looks_like' => ['nullable', 'array'],
            'good_home_environment_looks_like_other' => ['nullable', 'string', 'max:1000'],

            'self_description' => ['nullable', 'array'],
            'self_description_other' => ['nullable', 'string', 'max:1000'],
            'smokes' => ['boolean'],
            'deal_breakers_housemates' => ['nullable', 'string', 'max:1000'],
            'cultural_religious_practices' => ['nullable', 'string', 'max:1000'],
            'interests_hobbies' => ['nullable', 'string', 'max:1000'],

            'move_in_availability' => ['nullable', Rule::in(['ASAP', 'Within 1–3 months', 'Within 3–6 months', 'Just exploring options'])],
            'current_living_situation' => ['nullable', Rule::in(['SIL or SDA accommodation', 'Group home', 'With family', 'Living alone', 'Other'])],
            'current_living_situation_other' => ['nullable', 'string', 'max:1000'],
            'contact_for_suitable_match' => ['boolean'],
            'preferred_contact_method_match' => ['nullable', Rule::in(['Phone', 'Email', 'Via support coordinator', 'Other'])],
            'preferred_contact_method_match_other' => ['nullable', 'string', 'max:1000'],

            'street_address' => ['required', 'string', 'max:255'],
            'suburb' => ['required', 'string', 'max:255'],
            'state' => ['required', 'string', 'max:255'],
            'post_code' => ['required', 'string', 'max:10'],

            'health_report_file' => ['nullable', 'file', 'mimes:pdf,doc,docx,jpg,jpeg,png', 'max:2048'],
            'health_report_text' => ['nullable', 'string', 'max:5000'],
        ];

        if ($request->filled('gender_identity') && $request->input('gender_identity') === 'Other') {
            $rules['gender_identity_other'] = ['required', 'string', 'max:255'];
        }
        if ($request->filled('current_living_situation') && $request->input('current_living_situation') === 'Other') {
            $rules['current_living_situation_other'] = ['required', 'string', 'max:1000'];
        }
        if ($request->has('uses_assistive_technology_mobility_aids') && $request->input('uses_assistive_technology_mobility_aids')) {
            $rules['assistive_technology_mobility_aids_list'] = ['required', 'string', 'max:1000'];
        }
        if ($request->has('pets_in_home_preference') && $request->input('pets_in_home_preference') === 'Have pets') {
            $rules['own_pet_type'] = ['required', 'string', 'max:255'];
        }
        if ($request->has('contact_for_suitable_match') && $request->input('contact_for_suitable_match') && $request->input('preferred_contact_method_match') === 'Other') {
            $rules['preferred_contact_method_match_other'] = ['required', 'string', 'max:1000'];
        }

        $validatedData = $request->validate($rules);

        // Handle boolean checkboxes explicitly based on schema
        $validatedData['is_participant_best_contact'] = $request->has('is_participant_best_contact');
        $validatedData['has_support_coordinator'] = $request->has('has_support_coordinator');
        $validatedData['uses_assistive_technology_mobility_aids'] = $request->has('uses_assistive_technology_mobility_aids');
        $validatedData['smokes'] = $request->has('smokes');
        $validatedData['contact_for_suitable_match'] = $request->has('contact_for_suitable_match');
        
        // Handle JSON fields
        $validatedData['pronouns'] = json_encode($request->input('pronouns'));
        $validatedData['languages_spoken'] = json_encode($request->input('languages_spoken'));
        $validatedData['daily_living_support_needs'] = json_encode($request->input('daily_living_support_needs'));
        $validatedData['preferred_sil_locations'] = json_encode($request->input('preferred_sil_locations'));
        $validatedData['housemate_preferences'] = json_encode($request->input('housemate_preferences'));
        $validatedData['good_home_environment_looks_like'] = json_encode($request->input('good_home_environment_looks_like'));
        $validatedData['self_description'] = json_encode($request->input('self_description'));

        $validatedData['added_by_user_id'] = $coordinator->id;
        $validatedData['support_coordinator_id'] = $coordinator->id;

        $validatedData['health_report_path'] = null;
        if ($request->hasFile('health_report_file')) {
            $filePath = $request->file('health_report_file')->store('health_reports', 'public');
            $validatedData['health_report_path'] = $filePath;
        }

        $participant = Participant::create($validatedData);

        $participant->participant_code_name = 'PA' . str_pad($participant->id, 4, '0', STR_PAD_LEFT);
        $participant->save();

        return redirect()->route('sc.participants.list')->with('success', 'Participant added successfully!');
    }

    /**
     * Display the specified participant's profile.
     */
    public function showParticipant(Participant $participant)
    {
        if ($participant->support_coordinator_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        return view('supcoor.participants.show', compact('participant'));
    }

    /**
     * Show the form for editing the specified participant.
     */
    public function editParticipant(Participant $participant)
    {
        if ($participant->support_coordinator_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $primaryDisabilities = [
            'Physical Disability', 'Intellectual Disability', 'Sensory Disability',
            'Psychosocial Disability', 'Autism Spectrum Disorder', 'Neurological Disability',
            'Other'
        ];
        $currentLivingSituations = [
            'Private Rental', 'Living with Family', 'Shared Accommodation',
            'Supported Independent Living (SIL)', 'Specialist Disability Accommodation (SDA)',
            'Group Home', 'Boarding House', 'Homeless/Unstable'
        ];
        $genderIdentities = ['Female', 'Male', 'Non-binary', 'Prefer not to say', 'Other'];
        $ndisPlanManagers = ['Self-managed', 'Plan-managed', 'NDIA-managed', 'Not sure'];
        $silFundingStatuses = ['Yes', 'No', 'Not sure'];
        $contactMethods = ['Phone', 'Email', 'Either'];
        $preferredHousemateNumbers = ['1', '2', '3+', 'No preference'];
        $accessibilityNeeds = ['Fully accessible', 'Some modifications required', 'No specific needs'];
        $petPreferences = ['Have pets', 'Can live with pets', 'Do not want to live with pets'];
        $moveInAvailabilities = ['ASAP', 'Within 1–3 months', 'Within 3–6 months', 'Just exploring options'];
        $aboriginalTorresStraitIslanderOptions = ['Yes', 'No', 'Prefer not to say'];
        $medicationAdminHelpOptions = ['Yes', 'No', 'Sometimes'];
        $behaviourSupportPlanStatuses = ['Yes', 'No', 'In development'];
        $preferredContactMatchMethods = ['Phone', 'Email', 'Via support coordinator', 'Other'];

        $pronounOptions = ['She / Her', 'He / Him', 'They / Them', 'Other'];
        $dailyLivingSupportNeedsOptions = [
            'Personal care', 'Medication management', 'Meal preparation',
            'Household tasks', 'Community access', 'Transport', 'Financial management', 'Other'
        ];
        $housematePreferencesOptions = ['Male', 'Female', 'Mixed', 'No preference', 'Other'];
        $goodHomeEnvironmentLooksLikeOptions = [
            'Quiet', 'Social', 'Organized', 'Relaxed', 'Structured', 'Independent', 'Supportive', 'Other'
        ];
        $selfDescriptionOptions = [
            'Quiet', 'Social', 'Independent', 'Needs support', 'Organized', 'Relaxed', 'Active', 'Creative', 'Other'
        ];

        return view('supcoor.participants.edit', compact(
            'participant', 'primaryDisabilities', 'currentLivingSituations', 'genderIdentities',
            'ndisPlanManagers', 'silFundingStatuses', 'contactMethods',
            'preferredHousemateNumbers', 'accessibilityNeeds', 'petPreferences',
            'moveInAvailabilities', 'aboriginalTorresStraitIslanderOptions',
            'medicationAdminHelpOptions', 'behaviourSupportPlanStatuses',
            'preferredContactMatchMethods', 'pronounOptions', 'dailyLivingSupportNeedsOptions',
            'housematePreferencesOptions', 'goodHomeEnvironmentLooksLikeOptions', 'selfDescriptionOptions'
        ));
    }

    /**
     * Update the specified participant in storage.
     */
    public function updateParticipant(Request $request, Participant $participant)
    {
        if ($participant->support_coordinator_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $rules = [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'participant_email' => ['nullable', 'email', 'max:255'],
            'participant_phone' => ['nullable', 'string', 'max:255'],
            'participant_contact_method' => ['nullable', Rule::in(['Phone', 'Email', 'Either'])],
            'is_participant_best_contact' => ['boolean'],

            'date_of_birth' => ['nullable', 'date'],
            'gender_identity' => ['nullable', Rule::in(['Female', 'Male', 'Non-binary', 'Prefer not to say', 'Other'])],
            'gender_identity_other' => ['nullable', 'string', 'max:255'],
            'pronouns' => ['nullable', 'array'],
            'pronouns_other' => ['nullable', 'string', 'max:255'],
            'languages_spoken' => ['nullable', 'array'],
            'aboriginal_torres_strait_islander' => ['nullable', Rule::in(['Yes', 'No', 'Prefer not to say'])],

            'sil_funding_status' => ['nullable', Rule::in(['Yes', 'No', 'Not sure'])],
            'ndis_plan_review_date' => ['nullable', 'date'],
            'ndis_plan_manager' => ['nullable', Rule::in(['Self-managed', 'Plan-managed', 'NDIA-managed', 'Not sure'])],
            'has_support_coordinator' => ['boolean'],

            'daily_living_support_needs' => ['nullable', 'array'],
            'daily_living_support_needs_other' => ['nullable', 'string', 'max:1000'],
            'primary_disability' => ['nullable', 'string', 'max:255'],
            'secondary_disability' => ['nullable', 'string', 'max:255'],
            'estimated_support_hours_sil_level' => ['nullable', 'string', 'max:255'],
            'night_support_type' => ['nullable', Rule::in(['Active overnight', 'Sleepover', 'None'])],
            'uses_assistive_technology_mobility_aids' => ['in:0,1'],
            'assistive_technology_mobility_aids_list' => ['nullable', 'string', 'max:1000'],

            'medical_conditions_relevant' => ['nullable', 'string', 'max:1000'],
            'medication_administration_help' => ['nullable', Rule::in(['Yes', 'No', 'Sometimes'])],
            'behaviour_support_plan_status' => ['nullable', Rule::in(['Yes', 'No', 'In development'])],
            'behaviours_of_concern_housemates' => ['nullable', 'string', 'max:1000'],

            'preferred_sil_locations' => ['nullable', 'array'],
            'housemate_preferences' => ['nullable', 'array'],
            'housemate_preferences_other' => ['nullable', 'string', 'max:1000'],
            'preferred_number_of_housemates' => ['nullable', Rule::in(['1', '2', '3+', 'No preference'])],
            'accessibility_needs_in_home' => ['nullable', Rule::in(['Fully accessible', 'Some modifications required', 'No specific needs'])],
            'accessibility_needs_details' => ['nullable', 'string', 'max:1000'],
            'pets_in_home_preference' => ['nullable', Rule::in(['Have pets', 'Can live with pets', 'Do not want to live with pets'])],
            'own_pet_type' => ['nullable', 'string', 'max:255'],
            'good_home_environment_looks_like' => ['nullable', 'array'],
            'good_home_environment_looks_like_other' => ['nullable', 'string', 'max:1000'],

            'self_description' => ['nullable', 'array'],
            'self_description_other' => ['nullable', 'string', 'max:1000'],
            'smokes' => ['boolean'],
            'deal_breakers_housemates' => ['nullable', 'string', 'max:1000'],
            'cultural_religious_practices' => ['nullable', 'string', 'max:1000'],
            'interests_hobbies' => ['nullable', 'string', 'max:1000'],

            'move_in_availability' => ['nullable', Rule::in(['ASAP', 'Within 1–3 months', 'Within 3–6 months', 'Just exploring options'])],
            'current_living_situation' => ['nullable', Rule::in(['SIL or SDA accommodation', 'Group home', 'With family', 'Living alone', 'Other'])],
            'current_living_situation_other' => ['nullable', 'string', 'max:1000'],
            'contact_for_suitable_match' => ['boolean'],
            'preferred_contact_method_match' => ['nullable', Rule::in(['Phone', 'Email', 'Via support coordinator', 'Other'])],
            'preferred_contact_method_match_other' => ['nullable', 'string', 'max:1000'],

            'street_address' => ['required', 'string', 'max:255'],
            'suburb' => ['required', 'string', 'max:255'],
            'state' => ['required', 'string', 'max:255'],
            'post_code' => ['required', 'string', 'max:10'],

            'health_report_file' => ['nullable', 'file', 'mimes:pdf,doc,docx,jpg,jpeg,png', 'max:2048'],
            'health_report_text' => ['nullable', 'string', 'max:5000'],
        ];

        if ($request->filled('gender_identity') && $request->input('gender_identity') === 'Other') {
            $rules['gender_identity_other'] = ['required', 'string', 'max:255'];
        }
        if ($request->filled('current_living_situation') && $request->input('current_living_situation') === 'Other') {
            $rules['current_living_situation_other'] = ['required', 'string', 'max:1000'];
        }
        if ($request->has('uses_assistive_technology_mobility_aids') && $request->input('uses_assistive_technology_mobility_aids')) {
            $rules['assistive_technology_mobility_aids_list'] = ['required', 'string', 'max:1000'];
        }
        if ($request->has('pets_in_home_preference') && $request->input('pets_in_home_preference') === 'Have pets') {
            $rules['own_pet_type'] = ['required', 'string', 'max:255'];
        }
        if ($request->has('contact_for_suitable_match') && $request->input('contact_for_suitable_match') && $request->input('preferred_contact_method_match') === 'Other') {
            $rules['preferred_contact_method_match_other'] = ['required', 'string', 'max:1000'];
        }

        $validatedData = $request->validate($rules);

        // Handle boolean checkboxes
        $validatedData['is_participant_best_contact'] = $request->has('is_participant_best_contact');
        $validatedData['has_support_coordinator'] = $request->has('has_support_coordinator');
        $validatedData['uses_assistive_technology_mobility_aids'] = $request->has('uses_assistive_technology_mobility_aids');
        $validatedData['smokes'] = $request->has('smokes');
        $validatedData['contact_for_suitable_match'] = $request->has('contact_for_suitable_match');

        // Handle JSON fields (encode arrays to JSON strings for storage)
        $validatedData['pronouns'] = json_encode($request->input('pronouns'));
        $validatedData['languages_spoken'] = json_encode($request->input('languages_spoken'));
        $validatedData['daily_living_support_needs'] = json_encode($request->input('daily_living_support_needs'));
        $validatedData['preferred_sil_locations'] = json_encode($request->input('preferred_sil_locations'));
        $validatedData['housemate_preferences'] = json_encode($request->input('housemate_preferences'));
        $validatedData['good_home_environment_looks_like'] = json_encode($request->input('good_home_environment_looks_like'));
        $validatedData['self_description'] = json_encode($request->input('self_description'));

        if ($request->hasFile('health_report_file')) {
            if ($participant->health_report_path) {
                Storage::disk('public')->delete($participant->health_report_path);
            }
            $filePath = $request->file('health_report_file')->store('health_reports', 'public');
            $validatedData['health_report_path'] = $filePath;
        } else {
            unset($validatedData['health_report_path']);
        }

        $participant->update($validatedData);

        return redirect()->route('sc.participants.list')->with('success', 'Participant updated successfully!');
    }

    /**
     * Remove the specified participant from storage.
     */
    public function destroyParticipant(Participant $participant)
    {
        if ($participant->support_coordinator_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        if ($participant->health_report_path) {
            Storage::disk('public')->delete($participant->health_report_path);
        }

        $participant->delete();

        return redirect()->route('sc.participants.list')->with('success', 'Participant deleted successfully!');
    }

    /**
     * Support Center - View user's tickets
     */
    public function supportCenter(Request $request)
    {
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
        
        return view('supcoor.support-center.index', compact(
            'tickets', 
            'totalTickets', 
            'openTickets', 
            'inProgressTickets', 
            'resolvedTickets',
            'categories'
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
        
        return redirect()->route('sc.support-center.index')
            ->with('success', "Ticket {$ticket->ticket_number} created successfully!");
    }

    /**
     * View individual ticket
     */
    public function viewTicket(SupportTicket $ticket)
    {
        // Ensure user can only view their own tickets
        if ($ticket->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to ticket.');
        }
        
        $ticket->load(['category', 'assignedAdmin', 'comments.user']);
        $categories = SupportCategory::active()->ordered()->get();
        
        return view('supcoor.support-center.view', compact('ticket', 'categories'));
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
        
        return redirect()->route('sc.support-center.view', $ticket)
            ->with('success', 'Comment added successfully!');
    }

    /**
     * Show match requests page for support coordinators
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

        return view('supcoor.match-requests', compact('pendingRequests', 'sentRequests', 'acceptedRequests', 'pendingCount'));
    }
}