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
        Schema::create('providers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->onDelete('cascade');

            // SECTION 1: Organisation Details
            $table->string('organisation_name'); // Renamed from company_name for clarity
            $table->string('abn');
            $table->string('ndis_registration_number')->nullable();
            $table->json('provider_types'); // ['SIL Provider', 'SDA Provider', 'Both']

            $table->string('main_contact_name');
            $table->string('main_contact_role_title')->nullable();
            $table->string('phone_number'); // Renamed from contact_phone
            $table->string('email_address'); // Renamed from contact_email
            $table->string('website')->nullable();
            $table->string('office_address')->nullable(); // Renamed from address
            $table->string('office_suburb')->nullable(); // Renamed from suburb
            $table->string('office_state')->nullable(); // Renamed from state
            $table->string('office_post_code')->nullable(); // Renamed from post_code
            $table->json('states_operated_in'); // ['VIC', 'NSW', ...]

            // SECTION 2: Services Provided
            $table->json('sil_support_types')->nullable(); // ['24/7 support', 'Active overnight', ...]
            $table->text('sil_support_types_other')->nullable(); // For 'Other' text field
            $table->enum('clinical_team_involvement', ['Yes', 'No', 'In partnership with external providers'])->nullable();
            $table->json('staff_training_areas')->nullable(); // ['Delivering restrictive practices', 'Medication administration', ...]
            $table->text('staff_training_areas_other')->nullable(); // For 'Other' text field

            // Retained from your previous schema if still needed (not in new form spec)
            $table->string('plan')->nullable(); // e.g., 'basic', 'standard', 'advanced'
            $table->string('provider_code_name')->unique();
            $table->string('provider_logo_path')->nullable(); // Renamed from provider_logo for consistency with _path suffix

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('providers');
    }
};
