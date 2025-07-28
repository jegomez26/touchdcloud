<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User; // Make sure to import the User model
use App\Models\SupportCoordinator; // <--- NEW: Import the SupportCoordinator model
use Illuminate\Support\Facades\Hash; // Required for hashing passwords
use Carbon\Carbon; // Required for timestamps
use Illuminate\Support\Str; // <--- NEW: Required for Str::random() for sup_coor_code_name

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create Superadmin User
        if (!User::where('email', 'admin@silmatch.org')->exists()) {
            User::create([
                'first_name' => 'SIL Match',
                'last_name' => 'Admin',
                'email' => 'admin@silmatch.org',
                'password' => Hash::make('sil-Admin25'), // Always hash passwords!
                'role' => 'admin', // Ensure this matches your 'admin' enum value
                'email_verified_at' => Carbon::now(), // Mark as verified
                'profile_completed' => true, // Superadmin profile is considered complete
                'is_representative' => false,
                'relationship_to_participant' => null, // Added for completeness, explicitly null
                'representative_first_name' => null, // Not a representative
                'representative_last_name' => null,  // Not a representative
            ]);
            $this->command->info('Superadmin created successfully!');
        } else {
            $this->command->info('Superadmin already exists.');
        }

        // 2. Create Support Coordinator User
        $coordinatorUser = User::where('email', 'sc@silmatch.org')->first();
        if (!$coordinatorUser) {
            $coordinatorUser = User::create([
                'first_name' => 'Alice',
                'last_name' => 'Smith',
                'email' => 'sc@silmatch.org',
                'password' => Hash::make('sil-sc25'), // Always hash passwords!
                'role' => 'coordinator', // Ensure this matches your 'coordinator' enum value
                'email_verified_at' => Carbon::now(), // Mark as verified
                'profile_completed' => false,
                'is_representative' => false,
                'relationship_to_participant' => null, // Added for completeness
                'representative_first_name' => null, // Not a representative
                'representative_last_name' => null,  // Not a representative
            ]);
            $this->command->info('Support Coordinator User created successfully!');
        } else {
            $this->command->info('Support Coordinator User already exists.');
        }

        // 3. Create Support Coordinator Profile (in support_coordinators table)
        // This should only happen if the coordinator user was just created or if the profile doesn't exist
        if ($coordinatorUser && !SupportCoordinator::where('user_id', $coordinatorUser->id)->exists()) {
            SupportCoordinator::create([
                'user_id' => $coordinatorUser->id,
                'first_name' => $coordinatorUser->first_name, // Use name from user table
                'last_name' => $coordinatorUser->last_name,   // Use name from user table
                'middle_name' => null, // Assuming no middle name for seeder example
                'company_name' => 'Acme Support Services', // Example company name
                'abn' => '12345678901', // Example ABN (must be 11 digits for valid ABNs)
                'sup_coor_code_name' => 'SC-' . Str::upper(Str::random(6)), // Example code name
                'sup_coor_image' => null, // No image for seeder example
                'status' => 'verified', // Example status for seeder
                'verification_notes' => 'Seeded as verified coordinator.',
            ]);
            $this->command->info('Support Coordinator Profile created successfully!');
        } else if ($coordinatorUser) {
            $this->command->info('Support Coordinator Profile already exists.');
        }


        // 4. Create Participant User
        if (!User::where('email', 'indiv@silmatch.org')->exists()) {
            User::create([
                'first_name' => 'David',
                'last_name' => 'Owen',
                'email' => 'indiv@silmatch.org',
                'password' => Hash::make('sil-indiv25'), // Always hash passwords!
                'role' => 'participant', // Ensure this matches your 'participant' enum value
                'email_verified_at' => Carbon::now(), // Mark as verified
                'profile_completed' => false,
                'is_representative' => false,
                'relationship_to_participant' => null,
                'representative_first_name' => null, // Not a representative
                'representative_last_name' => null,  // Not a representative
            ]);
            $this->command->info('Participant created successfully!');
        } else {
            $this->command->info('Participant already exists.');
        }

        // 5. Create Provider User
        if (!User::where('email', 'provider@silmatch.org')->exists()) {
            User::create([
                'first_name' => 'Emily',
                'last_name' => 'Tan',
                'email' => 'provider@silmatch.org',
                'password' => Hash::make('sil-provider25'), // Changed password to avoid confusion
                'role' => 'provider', // Ensure this matches your 'provider' enum value
                'email_verified_at' => Carbon::now(), // Mark as verified
                'profile_completed' => false,
                'is_representative' => false,
                'relationship_to_participant' => null,
                'representative_first_name' => null, // Not a representative
                'representative_last_name' => null,  // Not a representative
            ]);
            $this->command->info('Provider created successfully!');
        } else {
            $this->command->info('Provider already exists.');
        }
    }
}