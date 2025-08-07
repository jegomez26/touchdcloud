<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('participants', function (Blueprint $table) {
            $table->id();

            // Link to the user account IF the participant is self-registered.
            // This is nullable because participants can be added by coordinators/providers/representatives.
            // No 'unique()' constraint here if a User (e.g., a representative) can manage multiple participants.
            // If the user_id is set, it means that user *is* this participant.
            $table->foreignId('user_id')->nullable()->unique()->constrained('users')->onDelete('cascade');

            // SECTION 0: Form Completion Details (related to who the profile is FOR)
            $table->string('first_name'); // Participant's first name
            $table->string('last_name'); // Participant's last name
            $table->string('middle_name')->nullable(); // Participant's middle name
            $table->string('participant_email')->nullable(); // Participant's direct email
            $table->string('participant_phone')->nullable(); // Participant's direct phone
            $table->enum('participant_contact_method', ['Phone', 'Email', 'Either'])->nullable();
            $table->boolean('is_participant_best_contact')->nullable(); // From form: "Is the participant the best person to contact"

            // SECTION 1: Basic Demographics
            $table->date('date_of_birth')->nullable();
            $table->enum('gender_identity', ['Female', 'Male', 'Non-binary', 'Prefer not to say', 'Other'])->nullable(); // Added 'Other' for completeness based on form pattern
            $table->string('gender_identity_other')->nullable(); // If 'Other' is selected for gender identity
            $table->json('pronouns')->nullable(); // Stores multiple selected pronouns, e.g., ['She / Her', 'They / Them']
            $table->string('pronouns_other')->nullable(); // If 'Other' is selected for pronouns
            $table->json('languages_spoken')->nullable(); // Use JSON for multiple languages/free text
            $table->enum('aboriginal_torres_strait_islander', ['Yes', 'No', 'Prefer not to say'])->nullable();

            // SECTION 2: NDIS Details
            $table->enum('sil_funding_status', ['Yes', 'No', 'Not sure'])->nullable();
            $table->date('ndis_plan_review_date')->nullable();
            $table->enum('ndis_plan_manager', ['Self-managed', 'Plan-managed', 'NDIA-managed', 'Not sure'])->nullable();
            $table->boolean('has_support_coordinator')->nullable(); // Yes/No/Not sure from form.

            // SECTION 3: Support Needs
            $table->json('daily_living_support_needs')->nullable(); // JSON for checkboxes (Personal care, Medication management, etc.)
            $table->text('daily_living_support_needs_other')->nullable(); // If 'Other' is selected for daily living needs
            $table->string('primary_disability')->nullable();
            $table->string('secondary_disability')->nullable();
            $table->string('estimated_support_hours_sil_level')->nullable(); // e.g., 1:3, 1:2
            $table->enum('night_support_type', ['Active overnight', 'Sleepover', 'None'])->nullable();
            $table->boolean('uses_assistive_technology_mobility_aids')->nullable();
            $table->text('assistive_technology_mobility_aids_list')->nullable(); // If uses_assistive_technology_mobility_aids is true

            // SECTION 4: Health & Safety
            $table->text('medical_conditions_relevant')->nullable();
            $table->enum('medication_administration_help', ['Yes', 'No', 'Sometimes'])->nullable();
            $table->enum('behaviour_support_plan_status', ['Yes', 'No', 'In development'])->nullable();
            $table->text('behaviours_of_concern_housemates')->nullable(); // If behaviour_support_plan_status is 'Yes'

            // SECTION 5: Living Preferences
            $table->json('preferred_sil_locations')->nullable(); // Store an array of locations, e.g., ['Mornington Peninsula VIC', 'South West Sydney']
            $table->json('housemate_preferences')->nullable(); // JSON for checkboxes (Male, Female, Mixed, etc.)
            $table->text('housemate_preferences_other')->nullable(); // If 'Other' selected for housemate preferences
            $table->enum('preferred_number_of_housemates', ['1', '2', '3+', 'No preference'])->nullable();
            $table->enum('accessibility_needs_in_home', ['Fully accessible', 'Some modifications required', 'No specific needs'])->nullable();
            $table->text('accessibility_needs_details')->nullable(); // If 'Some modifications required' or 'Fully accessible'
            $table->enum('pets_in_home_preference', ['Have pets', 'Can live with pets', 'Do not want to live with pets'])->nullable();
            $table->string('own_pet_type')->nullable(); // If 'Have pets'
            $table->json('good_home_environment_looks_like')->nullable(); // JSON for checkboxes (Quiet, Social, etc.)
            $table->text('good_home_environment_looks_like_other')->nullable(); // If 'Other' selected for good home environment

            // SECTION 6: Compatibility & Personality
            $table->json('self_description')->nullable(); // JSON for checkboxes (Quiet, Social, etc.)
            $table->text('self_description_other')->nullable(); // If 'Other' selected for self description
            $table->boolean('smokes')->nullable(); // Yes/No
            $table->text('deal_breakers_housemates')->nullable();
            $table->text('cultural_religious_practices')->nullable();
            $table->text('interests_hobbies')->nullable();

            // SECTION 7: Availability & Next Steps
            $table->enum('move_in_availability', ['ASAP', 'Within 1–3 months', 'Within 3–6 months', 'Just exploring options'])->nullable();
            $table->enum('current_living_situation', ['SIL or SDA accommodation', 'Group home', 'With family', 'Living alone', 'Other'])->nullable();
            $table->text('current_living_situation_other')->nullable(); // If 'Other' selected for current living situation
            $table->boolean('contact_for_suitable_match')->nullable(); // Yes/No
            $table->enum('preferred_contact_method_match', ['Phone', 'Email', 'Via support coordinator', 'Other'])->nullable(); // If contact_for_suitable_match is true
            $table->text('preferred_contact_method_match_other')->nullable(); // If 'Other' selected for preferred contact method match


            // Address Details (from your previous migration, not explicitly in the new form but good to keep for completeness)
            $table->string('street_address')->nullable();
            $table->string('suburb')->nullable();
            $table->string('state')->nullable();
            $table->string('post_code')->nullable();

            // Link to the user who is the *participant's* support coordinator (a user with 'coordinator' role)
            $table->foreignId('support_coordinator_id')->nullable()->constrained('users')->onDelete('set null');

            // The user who added this participant record (can be the participant themselves, a representative, coordinator, or provider)
            $table->foreignId('added_by_user_id')->constrained('users')->onDelete('cascade');

            // Unique code name for internal tracking
            $table->string('participant_code_name')->unique()->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('participants');
    }
};