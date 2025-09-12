<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Faker\Factory as Faker;

class ParticipantsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Use the 'en_AU' locale for Australian-specific data
        $faker = Faker::create('en_AU');

        // The user IDs for 'added_by' and 'support_coordinator'
        $addedByUserId = 6;
        $supportCoordinatorId = 6;

        // Custom data for specific fields
        $disabilities = ['Intellectual Disability', 'Dementia', 'Cerebral Palsy', 'Autism Spectrum Disorder', 'Acquired Brain Injury'];
        $pronouns = ['He / Him', 'She / Her', 'They / Them'];
        $languages = ['English', 'Auslan (Australian Sign Language)', 'Mandarin', 'Spanish'];
        $dailyLivingSupportNeeds = [
            'Assistance with mobility',
            'Community access',
            'Behavior support',
            'Personal care',
            'Medication management',
            'Meal preparation'
        ];
        $housematePreferences = [
            'Male housemates',
            'Female housemates',
            'Mixed gender',
            'Similar age group',
            'Cultural/religious compatibility',
            'No strong preference'
        ];
        $selfDescriptions = [
            'Quiet',
            'Social',
            'Routine-focused',
            'Independent',
            'Likes group activities',
            'Needs help building friendships',
            'Enjoys hobbies or creative outlets'
        ];
        $states = ['QLD', 'VIC', 'NSW', 'SA', 'WA', 'TAS', 'NT', 'ACT'];
        $contactMethods = ['Phone', 'Email', 'Either'];
        $ndisPlanManagers = ['Self-managed', 'Plan-managed', 'NDIA-managed', 'Not sure'];
        $nightSupportTypes = ['Active overnight', 'Sleepover', 'None'];
        $bsps = ['Yes', 'No', 'In development'];
        $moveInAvailability = ['ASAP', 'Within 1–3 months', 'Within 3–6 months', 'Just exploring options'];

        for ($i = 0; $i < 5; $i++) {
            // Build the preferred_sil_locations array
            $preferredLocations = [];
            for ($j = 0; $j < 2; $j++) {
                $preferredLocations[] = [
                    'state' => $faker->randomElement($states),
                    'suburb' => $faker->city,
                ];
            }
            
            DB::table('participants')->insert([
                'user_id' => null,
                'representative_user_id' => null,

                'first_name' => $faker->firstName,
                'last_name' => $faker->lastName,
                'middle_name' => $faker->optional()->firstName,
                'participant_email' => $faker->unique()->safeEmail,
                'participant_phone' => $faker->phoneNumber,
                'participant_contact_method' => $faker->randomElement($contactMethods),
                'is_participant_best_contact' => $faker->boolean,

                'date_of_birth' => $faker->date(),
                'gender_identity' => $faker->randomElement(['Female', 'Male', 'Non-binary', 'Prefer not to say']),
                'gender_identity_other' => null,
                'pronouns' => json_encode([$faker->randomElement($pronouns)]),
                'pronouns_other' => null,
                'languages_spoken' => json_encode($faker->randomElements($languages, $faker->numberBetween(1, 2))),
                'aboriginal_torres_strait_islander' => $faker->randomElement(['Yes', 'No', 'Prefer not to say']),

                'sil_funding_status' => $faker->randomElement(['Yes', 'No', 'Not sure']),
                'ndis_plan_review_date' => $faker->date(),
                'ndis_plan_manager' => $faker->randomElement($ndisPlanManagers),
                'has_support_coordinator' => true,

                'daily_living_support_needs' => json_encode($faker->randomElements($dailyLivingSupportNeeds, $faker->numberBetween(1, 3))),
                'daily_living_support_needs_other' => null,
                'primary_disability' => $faker->randomElement($disabilities),
                'secondary_disability' => $faker->optional()->word,
                'estimated_support_hours_sil_level' => '1:' . $faker->randomElement(['1', '2', '3']),
                'night_support_type' => $faker->randomElement($nightSupportTypes),
                'uses_assistive_technology_mobility_aids' => $faker->boolean,
                'assistive_technology_mobility_aids_list' => $faker->optional()->sentence,

                'medical_conditions_relevant' => $faker->text(100),
                'medication_administration_help' => $faker->randomElement(['Yes', 'No', 'Sometimes']),
                'behaviour_support_plan_status' => $faker->randomElement($bsps),
                'behaviours_of_concern_housemates' => $faker->optional()->text(100),

                'preferred_sil_locations' => json_encode($preferredLocations),
                'housemate_preferences' => json_encode($faker->randomElements($housematePreferences, $faker->numberBetween(1, 3))),
                'housemate_preferences_other' => null,
                'preferred_number_of_housemates' => $faker->randomElement(['1', '2', '3+', 'No preference']),
                'accessibility_needs_in_home' => 'Fully accessible',
                'accessibility_needs_details' => $faker->optional()->sentence,
                'pets_in_home_preference' => $faker->randomElement(['Have pets', 'Can live with pets', 'Do not want to live with pets']),
                'own_pet_type' => $faker->optional()->randomElement(['Dog', 'Cat', 'Bird']),
                'good_home_environment_looks_like' => json_encode(['Quiet', 'Social', 'Organized']),
                'good_home_environment_looks_like_other' => null,

                'self_description' => json_encode($faker->randomElements($selfDescriptions, $faker->numberBetween(2, 4))),
                'self_description_other' => null,
                'smokes' => $faker->boolean,
                'deal_breakers_housemates' => $faker->optional()->text(100),
                'cultural_religious_practices' => $faker->optional()->text(100),
                'interests_hobbies' => $faker->optional()->text(100),

                'move_in_availability' => $faker->randomElement($moveInAvailability),
                'current_living_situation' => 'With family',
                'current_living_situation_other' => null,
                'contact_for_suitable_match' => true,
                'preferred_contact_method_match' => 'Via support coordinator',
                'preferred_contact_method_match_other' => null,

                'street_address' => $faker->streetAddress,
                'suburb' => $faker->city,
                'state' => $faker->stateAbbr,
                'post_code' => $faker->postcode,

                'support_coordinator_id' => $supportCoordinatorId,
                'added_by_user_id' => $addedByUserId,
                'participant_code_name' => 'PA' . Str::random(5),

                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}