<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth; // Make sure to import Auth facade
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\Response; // Import Response for abort()

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
        $coordinatorCount = User::where('role', 'support_coordinator')->count();
        $providerCount = User::where('role', 'provider')->count();
        $ndisBusinessCount = User::where('role', 'ndis_business')->count();
        $superAdminCount = User::where('role', 'admin')->count(); // Assuming you still have 'super_admin' in DB
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

        return view('supadmin.dashboard-content', compact(
            'participantCount',
            'coordinatorCount',
            'providerCount',
            'ndisBusinessCount',
            'superAdminCount',
            'inactiveUserCount',
            'mappedRoles',          // Pass mapped roles for the pie chart
            'monthlyRegistrations'  // Pass monthly registrations for the line chart
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
        $this->authorizeSuperAdminAccess(); // Call the access check here

        $users = User::orderBy('created_at', 'desc')->paginate(20);
        return view('superadmin.dashboard.users.index', compact('users'));
    }

    /**
     * Activate a specific user.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function activateUser(User $user)
    {
        $this->authorizeSuperAdminAccess(); // Call the access check here

        // Prevent superadmin from deactivating themselves
        if ($user->id === auth()->id() && $user->role === 'admin') { // Use 'admin'
            return redirect()->back()->with('error', 'You cannot deactivate your own admin account.');
        }

        $user->is_active = true;
        $user->save();

        return redirect()->route('supadmin.users.index')->with('success', 'User ' . $user->email . ' has been activated.');
    }

    /**
     * Deactivate a specific user.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deactivateUser(User $user)
    {
        $this->authorizeSuperAdminAccess(); // Call the access check here

        // Prevent superadmin from deactivating themselves
        if ($user->id === auth()->id() && $user->role === 'admin') { // Use 'admin'
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
        $this->authorizeSuperAdminAccess(); // Call the access check here
        // ... rest of viewLogs method remains the same ...
        $logPath = storage_path('logs/laravel.log');

        if (!file_exists($logPath)) {
            return redirect()->back()->with('error', 'Log file not found.');
        }

        $lines = 200;
        $file = new \SplFileObject($logPath);
        $file->seek(PHP_INT_MAX);
        $lastLine = $file->key();

        $startLine = max(0, $lastLine - $lines);
        $logContent = [];

        $position = $file->getSize();
        $lineCount = 0;
        while ($position > 0 && $lineCount < $lines) {
            $char = fseek($file->openFile('r'), $position - 1, SEEK_SET) === 0 ? fgetc($file->openFile('r')) : false;
            if ($char === "\n" || $position === 1) {
                $line = rtrim(fgets($file->openFile('r'), $file->getSize()));
                if (!empty($line)) {
                    array_unshift($logContent, $line);
                    $lineCount++;
                }
            }
            $position--;
        }

        return view('superadmin.dashboard.logs.index', compact('logContent'));
    }

    public function downloadLogs()
    {
        $this->authorizeSuperAdminAccess(); // Call the access check here
        // ... rest of downloadLogs method remains the same ...
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
        $this->authorizeSuperAdminAccess(); // Call the access check here
        // ... rest of backupDataIndex method remains the same ...
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
        $this->authorizeSuperAdminAccess(); // Call the access check here
        // ... rest of createBackup method remains the same ...
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
        $this->authorizeSuperAdminAccess(); // Call the access check here
        // ... rest of downloadBackup method remains the same ...
        $path = 'backups/' . $filename;
        $disk = Storage::disk('local');

        if (!$disk->exists($path)) {
            return redirect()->back()->with('error', 'Backup file not found.');
        }

        return $disk->download($path, $filename);
    }

    public function deleteBackup($filename)
    {
        $this->authorizeSuperAdminAccess(); // Call the access check here
        // ... rest of deleteBackup method remains the same ...
        $path = 'backups/' . $filename;
        $disk = Storage::disk('local');

        if ($disk->exists($path)) {
            $disk->delete($path);
            return redirect()->route('superadmin.backup.index')->with('success', 'Backup deleted successfully.');
        }

        return redirect()->back()->with('error', 'Backup file not found.');
    }
}