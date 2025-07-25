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
            $table->foreignId('user_id')->nullable()->unique()->constrained('users')->onDelete('cascade');

            // Core Participant Information
            // These are the *actual* participant's names, regardless of who registered them.
            // If the user is a representative, the initial values for these come from user.representative_first_name/last_name.
            // If the user is self-registering, the initial values come from user.first_name/last_name.
            $table->string('first_name');
            $table->string('last_name');
            $table->string('middle_name')->nullable();
            $table->date('birthday')->nullable();
            $table->string('gender')->nullable();

            // Disability and Accommodation Details
            $table->json('disability_type')->nullable();
            $table->text('specific_disability')->nullable();
            $table->string('accommodation_type')->nullable();
            $table->enum('approved_accommodation_type', ['SDA', 'SIL'])->nullable();
            $table->text('behavior_of_concern')->nullable();

            // Address Details
            $table->string('street_address')->nullable();
            $table->string('suburb')->nullable();
            $table->string('state')->nullable();
            $table->string('post_code')->nullable();

            // Funding and Looking Status
            $table->boolean('is_looking_hm')->default(false);
            $table->boolean('has_accommodation')->default(false);
            $table->decimal('funding_amount_support_coor', 10, 2)->nullable();
            $table->decimal('funding_amount_accommodation', 10, 2)->nullable();

            // Associated User Relationships
            // Note: 'relative_name' and 'relationship_to_participant' for the *emergency contact* relative
            // The representative's relationship to the participant is on the users table.
            $table->string('relative_name')->nullable();
            $table->string('relative_phone')->nullable(); // Assuming you'd want emergency contact phone
            $table->string('relative_email')->nullable(); // Assuming you'd want emergency contact email
            $table->string('relative_relationship')->nullable(); // Corrected name for clarity if different from user.relationship_to_participant

            $table->foreignId('support_coordinator_id')->nullable()->constrained('support_coordinators')->onDelete('set null');
            // This 'representative_user_id' refers to the *User account* that is acting as a representative for *this participant*.
            $table->foreignId('representative_user_id')
                    ->nullable()
                    ->constrained('users')
                    ->onDelete('set null');
            $table->foreignId('added_by_user_id')->constrained('users')->onDelete('cascade');
            $table->string('participant_code_name')->unique()->nullable();
            // Unique Code Name (Removed from here, now stored in 'users' table)
            // $table->string('participant_code_name')->unique()->nullable(); // <-- REMOVE THIS LINE

            // Document Paths (for PDF uploads)
            $table->string('health_report_path')->nullable();
            $table->string('assessment_path')->nullable();

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