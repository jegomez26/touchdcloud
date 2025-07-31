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
            // user_id link: This is for the *user account* that *is* this participant (if self-registered)
            // or the primary user account managing this participant's profile.
            // If a single user can represent MANY participants, 'unique()' here should be reconsidered.
            // If it's a 1-to-1 relationship (a user account IS a participant), then 'unique()' is correct.
            $table->foreignId('user_id')->nullable()->unique()->constrained('users')->onDelete('cascade');

            // Core Participant Information
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

            // Associated User Relationships (Emergency Contact Information)
            $table->string('relative_name')->nullable();
            $table->string('relative_phone')->nullable();
            $table->string('relative_email')->nullable();
            $table->string('relative_relationship')->nullable();

            // Linking to Users (Support Coordinator, Representative, Adder)
            $table->foreignId('support_coordinator_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('representative_user_id')
                    ->nullable()
                    ->constrained('users')
                    ->onDelete('set null'); // This is the user account *acting* as a representative for this participant
            $table->foreignId('added_by_user_id')->constrained('users')->onDelete('cascade'); // The user who added this participant record

            // New: Participant Code Name - UNIQUE for each participant
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