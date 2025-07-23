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
            $table->string('first_name');
            $table->string('last_name');
            $table->string('middle_name')->nullable();
            $table->date('birthday')->nullable();
            $table->string('gender')->nullable(); // New: Gender field

            // Disability and Accommodation Details
            $table->json('disability_type')->nullable(); 
            $table->text('specific_disability')->nullable();
            $table->string('accommodation_type')->nullable();
            $table->enum('approved_accommodation_type', ['SDA', 'SIL'])->nullable(); // New: Approved Accommodation Type
            $table->text('behavior_of_concern')->nullable(); // New: Behavior of Concern (BOC)

            // Address Details
            $table->string('street_address')->nullable();
            $table->string('suburb')->nullable();
            $table->string('state')->nullable();
            $table->string('post_code')->nullable();

            // Funding and Looking Status
            $table->boolean('is_looking_hm')->default(false);
            $table->boolean('has_accommodation')->default(false);
            $table->decimal('funding_amount_support_coor', 10, 2)->nullable(); // New: Funding for Support Coordinator
            $table->decimal('funding_amount_accommodation', 10, 2)->nullable(); // New: Funding for Accommodation

            // Associated User Relationships
            $table->string('relative_name')->nullable();
            $table->foreignId('support_coordinator_id')->nullable()->constrained('support_coordinators')->onDelete('set null');
            $table->foreignId('representative_user_id')
                  ->nullable()
                  ->constrained('users')
                  ->onDelete('set null');
            $table->foreignId('added_by_user_id')->constrained('users')->onDelete('cascade');

            // Unique Code Name
            $table->string('participant_code_name')->unique()->nullable();

            // Document Paths (for PDF uploads)
            $table->string('health_report_path')->nullable(); // New: Path to Health Report PDF
            $table->string('assessment_path')->nullable();   // New: Path to Assessment PDF

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