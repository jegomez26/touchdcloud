<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User; // Make sure to import the User model
use Illuminate\Support\Facades\Hash; // Required for hashing passwords
use Carbon\Carbon; // Required for timestamps

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if the superadmin already exists to prevent duplicates
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
                'representative_first_name' => null, // Not a representative
                'representative_last_name' => null,  // Not a representative
            ]);

            $this->command->info('Superadmin created successfully!');
        } else {
            $this->command->info('Superadmin already exists.');
        }

        if (!User::where('email', 'sc@silmatch.org')->exists()) {
            User::create([
                'first_name' => 'Alice',
                'last_name' => 'Smith',
                'email' => 'sc@silmatch.org',
                'password' => Hash::make('sil-sc25'), // Always hash passwords!
                'role' => 'coordinator', // Ensure this matches your 'admin' enum value
                'email_verified_at' => Carbon::now(), // Mark as verified
                'profile_completed' => false, 
                'is_representative' => false,
                'representative_first_name' => null, // Not a representative
                'representative_last_name' => null,  // Not a representative
            ]);

            $this->command->info('Support Coordinator created successfully!');
        } else {
            $this->command->info('Support Coordinator already exists.');
        }

        if (!User::where('email', 'indiv@silmatch.org')->exists()) {
            User::create([
                'first_name' => 'David',
                'last_name' => 'Owen',
                'email' => 'indiv@silmatch.org',
                'password' => Hash::make('sil-indiv25'), // Always hash passwords!
                'role' => 'coordinator', // Ensure this matches your 'admin' enum value
                'email_verified_at' => Carbon::now(), // Mark as verified
                'profile_completed' => false, 
                'is_representative' => false,
                'representative_first_name' => null, // Not a representative
                'representative_last_name' => null,  // Not a representative
            ]);

            $this->command->info('Participant created successfully!');
        } else {
            $this->command->info('Participant already exists.');
        }

        if (!User::where('email', 'provider@silmatch.org')->exists()) {
            User::create([
                'first_name' => 'Emily',
                'last_name' => 'Tan',
                'email' => 'provider@silmatch.org',
                'password' => Hash::make('sil-indiv25'), // Always hash passwords!
                'role' => 'provider', // Ensure this matches your 'admin' enum value
                'email_verified_at' => Carbon::now(), // Mark as verified
                'profile_completed' => false, 
                'is_representative' => false,
                'representative_first_name' => null, // Not a representative
                'representative_last_name' => null,  // Not a representative
            ]);

            $this->command->info('Provider created successfully!');
        } else {
            $this->command->info('Provider already exists.');
        }
    }
}