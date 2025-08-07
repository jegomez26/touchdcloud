<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\SupportCoordinator;
use App\Models\Provider; // NEW: Import the Provider model
use App\Models\Property; // NEW: Import the Property model (assuming accommodations was renamed)
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Support\Str;

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
                'password' => Hash::make('sil-Admin25'),
                'role' => 'admin',
                'email_verified_at' => Carbon::now(),
                'profile_completed' => true,
                'is_active' => true, // Ensure this is set
            ]);
            $this->command->info('Superadmin created successfully! ✨');
        } else {
            $this->command->info('Superadmin already exists. ℹ️');
        }

        // 2. Create Support Coordinator User
        $coordinatorUser = User::where('email', 'sc@silmatch.org')->first();
        if (!$coordinatorUser) {
            $coordinatorUser = User::create([
                'first_name' => 'Alice',
                'last_name' => 'Smith',
                'email' => 'sc@silmatch.org',
                'password' => Hash::make('sil-sc25'),
                'role' => 'coordinator',
                'email_verified_at' => Carbon::now(),
                'profile_completed' => false, // Will be true once SupportCoordinator profile is created
                'is_active' => true,
            ]);
            $this->command->info('Support Coordinator User created successfully! 👩‍💼');
        } else {
            $this->command->info('Support Coordinator User already exists. ℹ️');
        }

        // 3. Create Support Coordinator Profile (in support_coordinators table)
        if ($coordinatorUser && !SupportCoordinator::where('user_id', $coordinatorUser->id)->exists()) {
            SupportCoordinator::create([
                'user_id' => $coordinatorUser->id,
                'first_name' => $coordinatorUser->first_name,
                'last_name' => $coordinatorUser->last_name,
                'middle_name' => null,
                'company_name' => 'Acme Support Services',
                'abn' => '12345678901',
                'sup_coor_code_name' => 'SC' . Str::upper(Str::random(6)),
                'profile_picture_path' => null, // Updated column name
                'status' => 'verified',
                'verification_notes' => 'Seeded as verified coordinator.',
            ]);
            // Update the user's profile_completed status after creating their profile
            $coordinatorUser->update(['profile_completed' => true]);
            $this->command->info('Support Coordinator Profile created successfully! ✅');
        } else if ($coordinatorUser) {
            $this->command->info('Support Coordinator Profile already exists. ℹ️');
        }

        // 4. Create Participant User
        if (!User::where('email', 'indiv@silmatch.org')->exists()) {
            User::create([
                'first_name' => 'David',
                'last_name' => 'Owen',
                'email' => 'indiv@silmatch.org',
                'password' => Hash::make('sil-indiv25'),
                'role' => 'participant',
                'email_verified_at' => Carbon::now(),
                'profile_completed' => false,
                'is_active' => true,
            ]);
            $this->command->info('Participant User created successfully! 🧑‍🦽');
        } else {
            $this->command->info('Participant User already exists. ℹ️');
        }

        // 5. Create Provider User
        $providerUser = User::where('email', 'provider@silmatch.org')->first();
        if (!$providerUser) {
            $providerUser = User::create([
                'first_name' => 'Emily',
                'last_name' => 'Tan',
                'email' => 'provider@silmatch.org',
                'password' => Hash::make('sil-provider25'),
                'role' => 'provider',
                'email_verified_at' => Carbon::now(),
                'profile_completed' => false, // Will be true once Provider profile is created
                'is_active' => true,
            ]);
            $this->command->info('Provider User created successfully! 🏢');
        } else {
            $this->command->info('Provider User already exists. ℹ️');
        }

        // 6. Create Provider Profile (in providers table)
        if ($providerUser && !Provider::where('user_id', $providerUser->id)->exists()) {
            Provider::create([
                'user_id' => $providerUser->id,
                'organisation_name' => 'Bright Future Homes',
                'abn' => '98765432109',
                'ndis_registration_number' => 'NDIS12345',
                'provider_types' => ['SIL Provider', 'SDA Provider', 'Both'], // JSON array
                'main_contact_name' => $providerUser->first_name . ' ' . $providerUser->last_name,
                'main_contact_role_title' => 'Director',
                'phone_number' => '0412345678',
                'email_address' => $providerUser->email,
                'website' => 'https://www.brightfuturehomes.com.au',
                'office_address' => '123 Sunny St',
                'office_suburb' => 'Sunshine',
                'office_state' => 'VIC',
                'office_post_code' => '3000',
                'states_operated_in' => ['VIC', 'NSW', 'QLD'], // JSON array
                'sil_support_types' => ['24/7 support', 'High-intensity supports'], // JSON array
                'sil_support_types_other' => null,
                'clinical_team_involvement' => 'Yes',
                'staff_training_areas' => ['Medication administration', 'Manual handling', 'Complex needs'], // JSON array
                'staff_training_areas_other' => null,
                'plan' => 'Growth Plan', // Assuming they start on Growth
                'provider_code_name' => 'PRV' . Str::upper(Str::random(6)),
                'provider_logo_path' => null, // No logo for seeder example
            ]);
            // Update the user's profile_completed status after creating their profile
            $providerUser->update(['profile_completed' => true]);
            $this->command->info('Provider Profile created successfully! ✅');
        } else if ($providerUser) {
            $this->command->info('Provider Profile already exists. ℹ️');
        }

        

    }
}