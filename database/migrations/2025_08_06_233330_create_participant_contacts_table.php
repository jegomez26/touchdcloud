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
        Schema::create('participant_contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('participant_id')->constrained('participants')->onDelete('cascade'); // Links to the participant profile
            $table->string('full_name');
            $table->string('relationship_to_participant')->nullable(); // e.g., "Family member", "Carer", "Public Guardian", "Support Worker", "Other"
            $table->string('organisation')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('email_address')->nullable();
            $table->enum('preferred_method_of_contact', ['Phone', 'Email', 'Either'])->nullable();
            $table->enum('consent_to_speak_on_behalf', ['Yes', 'No', 'Consent pending or unsure'])->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('participant_contacts');
    }
};