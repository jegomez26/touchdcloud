<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\SupportCoordinator;
use App\Models\Participant;
use App\Models\Provider;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log; // Added for Log::error, etc.
use Illuminate\Support\Facades\Mail; // Added for Mail::to
use App\Mail\SupportCoordinatorApproved; // Your Mailable for approval
use App\Mail\SupportCoordinatorRejected; // Your Mailable for rejection
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\Response;

class SuperAdminDashboardController extends Controller
{
    /**
     * Common method to check for super_admin (or admin) role.
     * This replaces the middleware logic.
     *
     * @return void|\Illuminate\Http\Response
     */
    protected function authorizeSuperAdminAccess()
    {
        // Check if user is logged in
        if (!Auth::check()) {
            // Redirect to login or show 403. Using 403 directly for simplicity,
            // but a redirect to login is often more user-friendly here.
            abort(Response::HTTP_FORBIDDEN, 'You must be logged in to access this page.');
        }

        // Check if the authenticated user has the 'super_admin' role (or 'admin' based on your decision)
        if (Auth::user()->role !== 'admin') { // Use 'admin' as per your last clarification
            abort(Response::HTTP_FORBIDDEN, 'Unauthorized access. You do not have super admin privileges.');
        }
    }

    public function manageSupportCoordinators()
    {
        // Add authorizeSuperAdminAccess check here as well
        $this->authorizeSuperAdminAccess();

        $pendingCoordinators = SupportCoordinator::with('user')
                                 ->where('status', 'pending_verification')
                                 ->get();

        $allCoordinators = SupportCoordinator::with('user')
                                ->orderBy('created_at', 'desc')
                                ->get(); // Fetch all coordinators for the bottom table

        // Calculate statistics
        $totalCoordinators = SupportCoordinator::count();
        $verifiedCoordinators = SupportCoordinator::where('status', 'verified')->count();
        $pendingCount = $pendingCoordinators->count();
        $newThisWeekCount = SupportCoordinator::where('created_at', '>=', now()->subWeek())->count();

        return view('supadmin.support-coordinators.index', compact(
            'pendingCoordinators', 
            'allCoordinators',
            'totalCoordinators',
            'verifiedCoordinators', 
            'pendingCount',
            'newThisWeekCount'
        ));
    }

    public function approveSupportCoordinator(SupportCoordinator $coordinator)
    {
        // Add authorizeSuperAdminAccess check here as well
        $this->authorizeSuperAdminAccess();

        $message = 'Support Coordinator ' . $coordinator->first_name . ' ' . $coordinator->last_name . ' has been approved.';
        $type = 'success';

        try {
            // 1. Update status
            $coordinator->update(['status' => 'verified', 'verification_notes' => null]);

            // 2. Attempt to send approval email
            try {
                if ($coordinator->user && $coordinator->user->email) {
                    Mail::to($coordinator->user->email)->send(new SupportCoordinatorApproved($coordinator));
                    Log::info('Approval email sent to: ' . $coordinator->user->email);
                    $message .= ' An approval email has been sent.';
                } else {
                    Log::warning('Could not send approval email: User or email missing for coordinator ID ' . $coordinator->id);
                    $message .= ' However, the approval email could not be sent as the user\'s email is missing.';
                    $type = 'warning'; // Change type to warning if email fails but approval succeeds
                }
            } catch (\Exception $emailException) {
                // Catch specifically email-related exceptions
                Log::error('Failed to send approval email to ' . ($coordinator->user->email ?? 'N/A') . ': ' . $emailException->getMessage(), ['exception' => $emailException, 'coordinator_id' => $coordinator->id]);
                $message .= ' However, there was an issue sending the approval email. Please check logs for details.';
                $type = 'warning'; // Change type to warning if email fails but approval succeeds
            }

        } catch (\Exception $e) {
            // Catch broader exceptions during status update or other critical steps
            Log::error('Failed to approve coordinator (ID: ' . $coordinator->id . '): ' . $e->getMessage(), ['exception' => $e]);
            $message = 'Failed to approve Support Coordinator. Please try again.';
            $type = 'error';
        }

        return redirect()->route('superadmin.support-coordinators.index')->with($type, $message);
    }

    /**
     * Reject a support coordinator.
     */
    public function rejectSupportCoordinator(Request $request, SupportCoordinator $coordinator)
    {
        // Add authorizeSuperAdminAccess check here as well
        $this->authorizeSuperAdminAccess();

        $request->validate([
            'verification_notes' => ['required', 'string', 'min:10', 'max:1000'],
        ]);

        $message = 'Support Coordinator ' . $coordinator->first_name . ' ' . $coordinator->last_name . ' has been rejected.';
        $type = 'success'; // Start with success, change to warning/error if needed

        try {
            $coordinator->update([
                'status' => 'rejected',
                'verification_notes' => $request->verification_notes
            ]);

            // Send rejection email
            try {
                if ($coordinator->user && $coordinator->user->email) {
                    Mail::to($coordinator->user->email)->send(new SupportCoordinatorRejected($coordinator, $request->verification_notes));
                    Log::info('Rejection email sent to: ' . $coordinator->user->email);
                    $message .= ' A rejection email has been sent.';
                } else {
                    Log::warning('Could not send rejection email: User or email missing for coordinator ID ' . $coordinator->id);
                    $message .= ' However, the rejection email could not be sent as the user\'s email is missing.';
                    $type = 'warning';
                }
            } catch (\Exception $emailException) {
                Log::error('Failed to send rejection email to ' . ($coordinator->user->email ?? 'N/A') . ': ' . $emailException->getMessage(), ['exception' => $emailException, 'coordinator_id' => $coordinator->id]);
                $message .= ' However, there was an issue sending the rejection email. Please check logs for details.';
                $type = 'warning';
            }

        } catch (\Exception $e) {
            Log::error('Error rejecting Support Coordinator: ' . $e->getMessage(), ['coordinator_id' => $coordinator->id, 'notes' => $request->verification_notes]);
            $message = 'Failed to reject Support Coordinator. Please try again.';
            $type = 'error';
        }

        return redirect()->route('superadmin.support-coordinators.index')->with($type, $message);
    }

    /**
     * Show the Super Admin Dashboard overview.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $this->authorizeSuperAdminAccess();

        // Basic Counts
        $participantCount = User::where('role', 'participant')->count();
        $coordinatorCount = User::where('role', 'coordinator')->count();
        $providerCount = User::where('role', 'provider')->count();
        $superAdminCount = User::where('role', 'admin')->count();
        $inactiveUserCount = User::where('is_active', false)->count();

        // Data for User Roles Pie Chart
        $userRolesData = User::select('role', DB::raw('count(*) as count'))
                                 ->groupBy('role')
                                 ->pluck('count', 'role')
                                 ->toArray();
        // Map database roles to more readable labels if needed
        $mappedRoles = [];
        foreach ($userRolesData as $role => $count) {
            $mappedRoles[ucwords(str_replace('_', ' ', $role))] = $count;
        }

        // Monthly Registrations per User Type (last 6 months)
        $monthlyRegistrationsByType = $this->getMonthlyRegistrationsByType();

        // Matches based on conversations
        $matchesData = $this->getMatchesData();

        // Top Analytics
        $topSuburbs = $this->getTopSuburbs();
        $topStates = $this->getTopStates();
        $topDisabilities = $this->getTopDisabilities();

        // Sitewide Map Data
        $sitewideMapData = $this->getSitewideMapData();

        // System Performance Metrics
        $performanceMetrics = $this->getPerformanceMetrics();

        // User Activity Analytics
        $userActivityData = $this->getUserActivityData();

        // Accepted Match Request Statistics
        $totalAcceptedMatchRequests = \App\Models\MatchRequest::where('status', 'accepted')->count();
        
        // Accepted match requests by provider/company
        // Get all provider users and count their accepted match requests
        $providerUsers = \App\Models\User::where('role', 'provider')->get();
        $acceptedMatchRequestsByProvider = $providerUsers->map(function($user) {
            $count = \App\Models\MatchRequest::where('status', 'accepted')
                ->where(function($query) use ($user) {
                    $query->where('sender_user_id', $user->id)
                          ->orWhere('receiver_user_id', $user->id);
                })
                ->count();
            return [
                'user_id' => $user->id,
                'name' => $user->first_name . ' ' . $user->last_name,
                'email' => $user->email,
                'count' => $count
            ];
        })
        ->filter(function($item) {
            return $item['count'] > 0; // Only show providers with accepted match requests
        })
        ->sortByDesc('count')
        ->values();

        // Accepted match requests by support coordinator
        // Get all coordinator users and count their accepted match requests
        $coordinatorUsers = \App\Models\User::where('role', 'coordinator')->get();
        $acceptedMatchRequestsBySupcoor = $coordinatorUsers->map(function($user) {
            $count = \App\Models\MatchRequest::where('status', 'accepted')
                ->where(function($query) use ($user) {
                    $query->where('sender_user_id', $user->id)
                          ->orWhere('receiver_user_id', $user->id);
                })
                ->count();
            return [
                'user_id' => $user->id,
                'name' => $user->first_name . ' ' . $user->last_name,
                'email' => $user->email,
                'count' => $count
            ];
        })
        ->filter(function($item) {
            return $item['count'] > 0; // Only show coordinators with accepted match requests
        })
        ->sortByDesc('count')
        ->values();

        return view('supadmin.dashboard-content', compact(
            'participantCount',
            'coordinatorCount',
            'providerCount',
            'superAdminCount',
            'inactiveUserCount',
            'mappedRoles',
            'monthlyRegistrationsByType',
            'matchesData',
            'topSuburbs',
            'topStates',
            'topDisabilities',
            'sitewideMapData',
            'performanceMetrics',
            'userActivityData',
            'totalAcceptedMatchRequests',
            'acceptedMatchRequestsByProvider',
            'acceptedMatchRequestsBySupcoor'
        ));
    }

    //--------------------------------------------------------------------------
    // User Activation/Deactivation 🚦
    //--------------------------------------------------------------------------

    /**
     * Display a list of all participants.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function manageParticipants(Request $request)
    {
        $this->authorizeSuperAdminAccess();

        $participants = Participant::with(['user', 'representativeUser', 'addedByUser'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // Calculate statistics
        $totalParticipants = Participant::count();
        $selfRegisteredCount = Participant::whereNotNull('user_id')->count();
        $newThisWeekCount = Participant::where('created_at', '>=', now()->subDays(7))->count();
        $uniqueSuburbsCount = Participant::whereNotNull('suburb')->distinct('suburb')->count();

        return view('supadmin.participants.index', compact(
            'participants', 
            'totalParticipants', 
            'selfRegisteredCount', 
            'newThisWeekCount', 
            'uniqueSuburbsCount'
        ));
    }

    /**
     * Display a list of all providers with their subscriptions.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function manageProviders(Request $request)
    {
        $this->authorizeSuperAdminAccess();

        $providers = Provider::with(['user', 'subscriptions'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // Calculate statistics
        $totalProviders = Provider::count();
        $activeSubscriptionsCount = Provider::whereHas('subscriptions', function($query) {
            $query->where('stripe_status', 'active')->orWhere('paypal_status', 'active');
        })->count();
        $expiredSubscriptionsCount = Provider::whereHas('subscriptions', function($query) {
            $query->where('stripe_status', 'expired')->orWhere('paypal_status', 'expired');
        })->count();
        $newThisWeekCount = Provider::where('created_at', '>=', now()->subDays(7))->count();

        return view('supadmin.providers.index', compact(
            'providers', 
            'totalProviders', 
            'activeSubscriptionsCount', 
            'expiredSubscriptionsCount', 
            'newThisWeekCount'
        ));
    }

    /**
     * Activate a specific user.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function activateUser(User $user)
    {
        $this->authorizeSuperAdminAccess();

        // Prevent superadmin from deactivating themselves
        if ($user->id === auth()->id() && $user->role === 'admin') {
            return redirect()->back()->with('error', 'You cannot deactivate your own admin account.');
        }

        $user->is_active = true;
        $user->save();

        return redirect()->route('superadmin.users.index')->with('success', 'User ' . $user->email . ' has been activated.'); // Corrected route name
    }

    /**
     * Deactivate a specific user.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deactivateUser(User $user)
    {
        $this->authorizeSuperAdminAccess();

        // Prevent superadmin from deactivating themselves
        if ($user->id === auth()->id() && $user->role === 'admin') {
            return redirect()->back()->with('error', 'You cannot deactivate your own admin account.');
        }

        $user->is_active = false;
        $user->save();

        return redirect()->route('superadmin.users.index')->with('success', 'User ' . $user->email . ' has been deactivated.');
    }

    //--------------------------------------------------------------------------
    // System Logs 📜
    //--------------------------------------------------------------------------

    public function viewLogs(Request $request)
    {
        $this->authorizeSuperAdminAccess();
        $logPath = storage_path('logs/laravel.log');

        if (!file_exists($logPath)) {
            return redirect()->back()->with('error', 'Log file not found.');
        }

        // --- Optimized log reading logic ---
        $linesToRead = 200;
        $handle = fopen($logPath, 'r');
        if (!$handle) {
            return redirect()->back()->with('error', 'Failed to open log file.');
        }

        $logContent = [];
        $lineCounter = 0;
        // Go to the end of the file
        fseek($handle, 0, SEEK_END);
        $pos = ftell($handle);

        // Read backwards
        // Keep a buffer to read lines properly from the end
        $buffer = '';
        while ($pos > 0 && $lineCounter < $linesToRead) {
            $pos--;
            fseek($handle, $pos);
            $char = fgetc($handle);

            if ($char === "\n") {
                // Prepend the line to the array (reversed to maintain order)
                $logContent[] = trim($buffer);
                $buffer = ''; // Reset buffer for next line
                $lineCounter++;
            } else {
                $buffer = $char . $buffer; // Prepend char to buffer
            }
        }
        // Add the last line if the file doesn't end with a newline or if we reached the beginning
        if ($buffer !== '' && $lineCounter < $linesToRead) {
            $logContent[] = trim($buffer);
            $lineCounter++;
        }

        // The lines are now in reverse order (oldest first due to how we appended)
        // Reverse again to get chronological (newest last) if needed for view,
        // but typically you want newest first for logs display, so let's adjust.
        // It should be $logContent = array_reverse($logContent); if reading from start,
        // but since we read backwards and prepend, it's already newest first.
        // Re-examine the previous logic... `fgets_forward` and `fgetc_reverse` are custom functions.
        // Let's use a standard backward file reader.

        // Corrected standard backward reading
        $file = new \SplFileObject($logPath, 'r');
        $file->seek(PHP_INT_MAX); // Go to end
        $currentLine = $file->key(); // Get the last line number

        $logContent = [];
        $lineLimit = 200; // Only read the last 200 lines
        $count = 0;

        while ($count < $lineLimit && $currentLine >= 0) {
            $file->seek($currentLine);
            $line = $file->current();
            $logContent[] = trim($line);
            $currentLine--;
            $count++;
        }
        $logContent = array_reverse($logContent); // Reverse to get chronological order (oldest to newest)


        fclose($handle); // Close the handle from the previous logic, if it's still open or used.
        // The SplFileObject approach doesn't require manual fopen/fclose for this logic.

        return view('supadmin.logs.index', compact('logContent'));
    }

    public function downloadLogs()
    {
        $this->authorizeSuperAdminAccess();
        $logPath = storage_path('logs/laravel.log');

        if (!file_exists($logPath)) {
            return redirect()->back()->with('error', 'Log file not found.');
        }

        return response()->download($logPath);
    }

    //--------------------------------------------------------------------------
    // Data Backup 💾
    //--------------------------------------------------------------------------

    public function backupDataIndex()
    {
        $this->authorizeSuperAdminAccess();
        $disk = Storage::disk('local');
        $files = $disk->files('backups');

        $backups = collect($files)->filter(function ($file) {
            return preg_match('/\.zip$/', $file) || preg_match('/\.sql$/', $file);
        })->map(function ($file) use ($disk) {
            return [
                'name' => basename($file),
                'path' => $file,
                'size' => $disk->size($file),
                'last_modified' => $disk->lastModified($file),
            ];
        })->sortByDesc('last_modified');

        return view('supadmin.backup.index', compact('backups'));
    }

    public function createBackup()
    {
        $this->authorizeSuperAdminAccess();
        try {
            Artisan::call('backup:run', ['--only-db' => false, '--only-files' => false]);
            $output = Artisan::output();
            return redirect()->route('superadmin.backup.index')->with('success', 'Backup initiated successfully! ' . $output);
        } catch (\Exception $e) {
            return redirect()->route('superadmin.backup.index')->with('error', 'Backup failed: ' . $e->getMessage());
        }
    }

    public function downloadBackup($filename)
    {
        $this->authorizeSuperAdminAccess();
        $path = 'backups/' . $filename;
        $disk = Storage::disk('local');

        if (!$disk->exists($path)) {
            return redirect()->back()->with('error', 'Backup file not found.');
        }

        return $disk->download($path, $filename);
    }

    public function deleteBackup($filename)
    {
        $this->authorizeSuperAdminAccess();
        $path = 'backups/' . $filename;
        $disk = Storage::disk('local');

        if ($disk->exists($path)) {
            $disk->delete($path);
            return redirect()->route('superadmin.backup.index')->with('success', 'Backup deleted successfully.');
        }

        return redirect()->back()->with('error', 'Backup file not found.');
    }

    /**
     * Format bytes to human readable format
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision) . ' ' . $units[$i];
    }

    /**
     * Get log level from log line
     */
    private function getLogLevel($line)
    {
        if (strpos($line, 'ERROR') !== false) return 'ERROR';
        if (strpos($line, 'WARNING') !== false) return 'WARNING';
        if (strpos($line, 'INFO') !== false) return 'INFO';
        if (strpos($line, 'DEBUG') !== false) return 'DEBUG';
        return 'INFO';
    }

    /**
     * Get CSS class for log level
     */
    private function getLogClass($line)
    {
        $level = $this->getLogLevel($line);
        return $level;
    }

    /**
     * Get monthly registrations by user type
     */
    private function getMonthlyRegistrationsByType()
    {
        $data = [];
        $today = Carbon::now();

        for ($i = 5; $i >= 0; $i--) {
            $month = $today->copy()->subMonths($i);
            $monthName = $month->format('M Y');

            $data[$monthName] = [
                'participants' => User::where('role', 'participant')
                    ->whereYear('created_at', $month->year)
                    ->whereMonth('created_at', $month->month)
                    ->count(),
                'coordinators' => User::where('role', 'coordinator')
                    ->whereYear('created_at', $month->year)
                    ->whereMonth('created_at', $month->month)
                    ->count(),
                'providers' => User::where('role', 'provider')
                    ->whereYear('created_at', $month->year)
                    ->whereMonth('created_at', $month->month)
                    ->count(),
            ];
        }

        return $data;
    }

    /**
     * Get matches data based on conversations
     */
    private function getMatchesData()
    {
        $totalConversations = Conversation::count();
        
        // Since there's no status column, we'll determine "active" conversations 
        // as those with recent messages (within last 30 days)
        $activeConversations = Conversation::whereHas('messages', function($query) {
            $query->where('created_at', '>=', now()->subDays(30));
        })->count();
        
        // For completed matches, we'll consider conversations with multiple messages
        // as potential matches (this is a heuristic since we don't have explicit status)
        $completedMatches = Conversation::withCount('messages')
            ->having('messages_count', '>', 1)
            ->count();
        
        $totalMessages = Message::count();

        return [
            'total_conversations' => $totalConversations,
            'active_conversations' => $activeConversations,
            'completed_matches' => $completedMatches,
            'total_messages' => $totalMessages,
            'match_rate' => $totalConversations > 0 ? round(($completedMatches / $totalConversations) * 100, 2) : 0
        ];
    }

    /**
     * Get top 10 suburbs with participant counts
     */
    private function getTopSuburbs()
    {
        return Participant::selectRaw('suburb, COUNT(*) as count')
            ->whereNotNull('suburb')
            ->where('suburb', '!=', '')
            ->groupBy('suburb')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get();
    }

    /**
     * Get top 10 states with participant counts
     */
    private function getTopStates()
    {
        return Participant::selectRaw('state, COUNT(*) as count')
            ->whereNotNull('state')
            ->where('state', '!=', '')
            ->groupBy('state')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get();
    }

    /**
     * Get top 10 disabilities with participant counts
     */
    private function getTopDisabilities()
    {
        return Participant::selectRaw('primary_disability, COUNT(*) as count')
            ->whereNotNull('primary_disability')
            ->where('primary_disability', '!=', '')
            ->groupBy('primary_disability')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get();
    }

    /**
     * Get sitewide map data with coordinates
     */
    private function getSitewideMapData()
    {
        return Participant::select('suburb', 'state', 'primary_disability', 'gender_identity')
            ->get()
            ->groupBy(function($participant) {
                $suburb = trim($participant->suburb);
                $state = trim($participant->state);
                
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
                
                // Mock coordinates for Australian suburbs
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
                    'Isaacs' => ['latitude' => -35.3500, 'longitude' => 149.1500],
                ];
                
                // Get coordinates for the suburb, or use state-based fallback
                if (!empty($suburb) && isset($mockCoordinates[$suburb])) {
                    $latitude = $mockCoordinates[$suburb]['latitude'];
                    $longitude = $mockCoordinates[$suburb]['longitude'];
                } elseif (!empty($state)) {
                    $stateCapitals = [
                        'NSW' => ['latitude' => -33.8688, 'longitude' => 151.2093],
                        'VIC' => ['latitude' => -37.8136, 'longitude' => 144.9631],
                        'QLD' => ['latitude' => -27.4698, 'longitude' => 153.0251],
                        'WA' => ['latitude' => -31.9505, 'longitude' => 115.8605],
                        'SA' => ['latitude' => -34.9285, 'longitude' => 138.6007],
                        'TAS' => ['latitude' => -42.8821, 'longitude' => 147.3272],
                        'NT' => ['latitude' => -12.4634, 'longitude' => 130.8456],
                        'ACT' => ['latitude' => -35.2809, 'longitude' => 149.1300]
                    ];
                    
                    if (isset($stateCapitals[$state])) {
                        $latitude = $stateCapitals[$state]['latitude'];
                        $longitude = $stateCapitals[$state]['longitude'];
                    } else {
                        $latitude = -25.2744;
                        $longitude = 133.7751;
                    }
                } else {
                    $latitude = -25.2744;
                    $longitude = 133.7751;
                }
                
                return (object) [
                    'location' => $locationKey,
                    'suburb' => $suburb,
                    'state' => $state,
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                    'participant_count' => $participants->count(),
                    'disabilities' => $participants->pluck('primary_disability')->filter()->values(),
                    'genders' => $participants->pluck('gender_identity')->filter()->values()
                ];
            })
            ->values();
    }

    /**
     * Get system performance metrics
     */
    private function getPerformanceMetrics()
    {
        $totalUsers = User::count();
        $activeUsers = User::where('is_active', true)->count();
        $totalParticipants = Participant::count();
        $totalProviders = Provider::count();
        $totalConversations = Conversation::count();
        $totalMessages = Message::count();

        return [
            'total_users' => $totalUsers,
            'active_users' => $activeUsers,
            'user_activity_rate' => $totalUsers > 0 ? round(($activeUsers / $totalUsers) * 100, 2) : 0,
            'total_participants' => $totalParticipants,
            'total_providers' => $totalProviders,
            'total_conversations' => $totalConversations,
            'total_messages' => $totalMessages,
            'avg_messages_per_conversation' => $totalConversations > 0 ? round($totalMessages / $totalConversations, 2) : 0
        ];
    }

    /**
     * Get user activity data
     */
    private function getUserActivityData()
    {
        $today = Carbon::now();
        $lastWeek = $today->copy()->subWeek();
        $lastMonth = $today->copy()->subMonth();

        return [
            // Since there's no last_login_at column, we'll use different metrics
            'recent_registrations_today' => User::where('created_at', '>=', $today->startOfDay())->count(),
            'recent_registrations_this_week' => User::where('created_at', '>=', $lastWeek)->count(),
            'recent_registrations_this_month' => User::where('created_at', '>=', $lastMonth)->count(),
            'active_users' => User::where('is_active', true)->count(),
            'inactive_users' => User::where('is_active', false)->count(),
            'profile_completion_rate' => User::count() > 0 ? round((User::where('profile_completed', true)->count() / User::count()) * 100, 2) : 0
        ];
    }

    //--------------------------------------------------------------------------
    // Participant Activation/Deactivation 🚦
    //--------------------------------------------------------------------------

    /**
     * Activate a participant's account.
     *
     * @param  \App\Models\Participant  $participant
     * @return \Illuminate\Http\RedirectResponse
     */
    public function activateParticipant(Participant $participant)
    {
        $this->authorizeSuperAdminAccess();

        // Prevent superadmin from deactivating themselves
        if ($participant->user && $participant->user->id === auth()->id() && $participant->user->role === 'admin') {
            return redirect()->back()->with('error', 'You cannot deactivate your own admin account.');
        }

        if ($participant->user) {
            $participant->user->is_active = true;
            $participant->user->save();
        }

        return redirect()->route('superadmin.participants.index')->with('success', 'Participant ' . ($participant->user ? $participant->user->email : 'ID: ' . $participant->id) . ' has been activated.');
    }

    /**
     * Deactivate a participant's account.
     *
     * @param  \App\Models\Participant  $participant
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deactivateParticipant(Participant $participant)
    {
        $this->authorizeSuperAdminAccess();

        // Prevent superadmin from deactivating themselves
        if ($participant->user && $participant->user->id === auth()->id() && $participant->user->role === 'admin') {
            return redirect()->back()->with('error', 'You cannot deactivate your own admin account.');
        }

        if ($participant->user) {
            $participant->user->is_active = false;
            $participant->user->save();
        }

        return redirect()->route('superadmin.participants.index')->with('success', 'Participant ' . ($participant->user ? $participant->user->email : 'ID: ' . $participant->id) . ' has been deactivated.');
    }

    //--------------------------------------------------------------------------
    // Provider Activation/Deactivation 🚦
    //--------------------------------------------------------------------------

    /**
     * Activate a provider's account.
     *
     * @param  \App\Models\Provider  $provider
     * @return \Illuminate\Http\RedirectResponse
     */
    public function activateProvider(Provider $provider)
    {
        $this->authorizeSuperAdminAccess();

        // Prevent superadmin from deactivating themselves
        if ($provider->user && $provider->user->id === auth()->id() && $provider->user->role === 'admin') {
            return redirect()->back()->with('error', 'You cannot deactivate your own admin account.');
        }

        if ($provider->user) {
            $provider->user->is_active = true;
            $provider->user->save();
        }

        return redirect()->route('superadmin.providers.index')->with('success', 'Provider ' . ($provider->user ? $provider->user->email : 'ID: ' . $provider->id) . ' has been activated.');
    }

    /**
     * Deactivate a provider's account.
     *
     * @param  \App\Models\Provider  $provider
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deactivateProvider(Provider $provider)
    {
        $this->authorizeSuperAdminAccess();

        // Prevent superadmin from deactivating themselves
        if ($provider->user && $provider->user->id === auth()->id() && $provider->user->role === 'admin') {
            return redirect()->back()->with('error', 'You cannot deactivate your own admin account.');
        }

        if ($provider->user) {
            $provider->user->is_active = false;
            $provider->user->save();
        }

        return redirect()->route('superadmin.providers.index')->with('success', 'Provider ' . ($provider->user ? $provider->user->email : 'ID: ' . $provider->id) . ' has been deactivated.');
    }

    //--------------------------------------------------------------------------
    // Support Coordinator Activation/Deactivation 🚦
    //--------------------------------------------------------------------------

    /**
     * Activate a support coordinator's account.
     *
     * @param  \App\Models\SupportCoordinator  $coordinator
     * @return \Illuminate\Http\RedirectResponse
     */
    public function activateSupportCoordinator(SupportCoordinator $coordinator)
    {
        $this->authorizeSuperAdminAccess();

        // Prevent superadmin from deactivating themselves
        if ($coordinator->user && $coordinator->user->id === auth()->id() && $coordinator->user->role === 'admin') {
            return redirect()->back()->with('error', 'You cannot deactivate your own admin account.');
        }

        if ($coordinator->user) {
            $coordinator->user->is_active = true;
            $coordinator->user->save();
        }

        return redirect()->route('superadmin.support-coordinators.index')->with('success', 'Support Coordinator ' . ($coordinator->user ? $coordinator->user->email : 'ID: ' . $coordinator->id) . ' has been activated.');
    }

    /**
     * Deactivate a support coordinator's account.
     *
     * @param  \App\Models\SupportCoordinator  $coordinator
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deactivateSupportCoordinator(SupportCoordinator $coordinator)
    {
        $this->authorizeSuperAdminAccess();

        // Prevent superadmin from deactivating themselves
        if ($coordinator->user && $coordinator->user->id === auth()->id() && $coordinator->user->role === 'admin') {
            return redirect()->back()->with('error', 'You cannot deactivate your own admin account.');
        }

        if ($coordinator->user) {
            $coordinator->user->is_active = false;
            $coordinator->user->save();
        }

        return redirect()->route('superadmin.support-coordinators.index')->with('success', 'Support Coordinator ' . ($coordinator->user ? $coordinator->user->email : 'ID: ' . $coordinator->id) . ' has been deactivated.');
    }

    /**
     * Show modal demo page
     */
    public function modalDemo()
    {
        $this->authorizeSuperAdminAccess();
        
        return view('supadmin.modal-demo');
    }

    public function testPrivilege()
    {
        $this->authorizeSuperAdminAccess();
        
        return view('supadmin.test-privilege');
    }

    /**
     * Support Center - Main page
     */
    public function supportCenter(Request $request)
    {
        $this->authorizeSuperAdminAccess();
        
        $query = \App\Models\SupportTicket::with(['user', 'assignedAdmin'])
            ->orderBy('created_at', 'desc');
        
        // Filter by status
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }
        
        // Filter by priority
        if ($request->has('priority') && $request->priority !== 'all') {
            $query->where('priority', $request->priority);
        }
        
        // Filter by type
        if ($request->has('type') && $request->type !== 'all') {
            $query->where('type', $request->type);
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
        
        $tickets = $query->paginate(20);
        
        // Statistics
        $totalTickets = \App\Models\SupportTicket::count();
        $openTickets = \App\Models\SupportTicket::where('status', 'open')->count();
        $inProgressTickets = \App\Models\SupportTicket::where('status', 'in_progress')->count();
        $resolvedTickets = \App\Models\SupportTicket::where('status', 'resolved')->count();
        $urgentTickets = \App\Models\SupportTicket::where('priority', 'urgent')->whereIn('status', ['open', 'in_progress', 'pending'])->count();
        
        // Get all admins for assignment
        $admins = \App\Models\User::whereIn('role', ['admin', 'super_admin'])->get();
        
        return view('supadmin.support-center.index', compact(
            'tickets', 
            'totalTickets', 
            'openTickets', 
            'inProgressTickets', 
            'resolvedTickets', 
            'urgentTickets',
            'admins'
        ));
    }

    /**
     * Update ticket status
     */
    public function updateTicketStatus(Request $request, \App\Models\SupportTicket $ticket)
    {
        $this->authorizeSuperAdminAccess();
        
        $request->validate([
            'status' => 'required|in:open,in_progress,pending,resolved,closed',
            'resolution_notes' => 'nullable|string|max:1000'
        ]);
        
        $ticket->status = $request->status;
        
        if ($request->status === 'resolved' && !$ticket->resolved_at) {
            $ticket->resolved_at = now();
        }
        
        if ($request->resolution_notes) {
            $ticket->resolution_notes = $request->resolution_notes;
        }
        
        $ticket->save();
        
        return redirect()->route('superadmin.support-center.index')
            ->with('success', "Ticket {$ticket->ticket_number} status updated to {$ticket->status_name}.");
    }

    /**
     * Assign ticket to admin
     */
    public function assignTicket(Request $request, \App\Models\SupportTicket $ticket)
    {
        $this->authorizeSuperAdminAccess();
        
        $request->validate([
            'assigned_to' => 'required|exists:users,id'
        ]);
        
        $ticket->assigned_to = $request->assigned_to;
        $ticket->save();
        
        $assignedAdmin = \App\Models\User::find($request->assigned_to);
        
        return redirect()->route('superadmin.support-center.index')
            ->with('success', "Ticket {$ticket->ticket_number} assigned to {$assignedAdmin->first_name} {$assignedAdmin->last_name}.");
    }

    /**
     * View individual ticket
     */
    public function viewTicket(\App\Models\SupportTicket $ticket)
    {
        $this->authorizeSuperAdminAccess();
        
        $ticket->load(['user', 'assignedAdmin']);
        $admins = \App\Models\User::whereIn('role', ['admin', 'super_admin'])->get();
        
        return view('supadmin.support-center.view', compact('ticket', 'admins'));
    }

    /**
     * Manage admins page
     */
    public function manageAdmins()
    {
        $this->authorizeSuperAdminAccess();

        // Get all admin users
        $admins = User::where('role', 'admin')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // Calculate statistics
        $totalAdmins = User::where('role', 'admin')->count();
        $activeAdmins = User::where('role', 'admin')->where('is_active', true)->count();
        $inactiveAdmins = User::where('role', 'admin')->where('is_active', false)->count();
        $newThisWeekCount = User::where('role', 'admin')->where('created_at', '>=', now()->subWeek())->count();

        return view('supadmin.admins.index', compact(
            'admins',
            'totalAdmins',
            'activeAdmins', 
            'inactiveAdmins',
            'newThisWeekCount'
        ));
    }

    /**
     * Create a new admin
     */
    public function createAdmin(Request $request)
    {
        $this->authorizeSuperAdminAccess();

        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|string|in:admin,super_admin',
            'privileges' => 'array',
            'privileges.*' => 'string|in:manage_users,manage_providers,manage_participants,manage_support_coordinators,manage_admins,view_logs,manage_backups'
        ]);

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'privileges' => $request->privileges ?? [],
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        return redirect()->route('superadmin.admins.index')->with('success', 'Admin ' . $user->email . ' has been created successfully.');
    }

    /**
     * Activate an admin
     */
    public function activateAdmin(User $admin)
    {
        $this->authorizeSuperAdminAccess();

        // Prevent superadmin from activating themselves
        if ($admin->id === auth()->id() && $admin->role === 'admin') {
            return redirect()->back()->with('error', 'You cannot modify your own admin account.');
        }

        $admin->is_active = true;
        $admin->save();

        return redirect()->route('superadmin.admins.index')->with('success', 'Admin ' . $admin->email . ' has been activated.');
    }

    /**
     * Deactivate an admin
     */
    public function deactivateAdmin(User $admin)
    {
        $this->authorizeSuperAdminAccess();

        // Prevent superadmin from deactivating themselves
        if ($admin->id === auth()->id() && $admin->role === 'admin') {
            return redirect()->back()->with('error', 'You cannot modify your own admin account.');
        }

        $admin->is_active = false;
        $admin->save();

        return redirect()->route('superadmin.admins.index')->with('success', 'Admin ' . $admin->email . ' has been deactivated.');
    }
}