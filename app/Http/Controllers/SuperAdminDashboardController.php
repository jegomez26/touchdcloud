<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\SupportCoordinator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
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

        return view('supadmin.support-coordinators.index', compact('pendingCoordinators', 'allCoordinators'));
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
        // Assuming ndis_business role exists for users as well, if not, adjust
        $ndisBusinessCount = User::where('role', 'ndis_business')->count();
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


        // Data for Monthly Registrations Line Chart (last 6 months example)
        $monthlyRegistrations = [];
        $today = Carbon::now();

        for ($i = 5; $i >= 0; $i--) {
            $month = $today->copy()->subMonths($i);
            $monthName = $month->format('M');
            $year = $month->format('Y');

            $count = User::whereYear('created_at', $year)
                          ->whereMonth('created_at', $month->month)
                          ->count();

            $monthlyRegistrations[$monthName . ' ' . $year] = $count;
        }

        return view('supadmin.dashboard-content', compact( // Corrected view path to superadmin.dashboard-content
            'participantCount',
            'coordinatorCount',
            'providerCount',
            'ndisBusinessCount',
            'superAdminCount',
            'inactiveUserCount',
            'mappedRoles',
            'monthlyRegistrations'
        ));
    }

    //--------------------------------------------------------------------------
    // User Activation/Deactivation 🚦
    //--------------------------------------------------------------------------

    /**
     * Display a list of all users for activation/deactivation.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function manageUsers(Request $request)
    {
        $this->authorizeSuperAdminAccess();

        $users = User::orderBy('created_at', 'desc')->paginate(20);
        return view('supadmin.dashboard.users.index', compact('users'));
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

        return view('superadmin.dashboard.logs.index', compact('logContent'));
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

        return view('superadmin.dashboard.backup.index', compact('backups'));
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
}