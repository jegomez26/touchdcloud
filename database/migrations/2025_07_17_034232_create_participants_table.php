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
            $table->foreignId('user_id')->unique()->constrained('users')->onDelete('cascade');
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->date('birthday');
            $table->string('disability_type');
            $table->text('specific_disability')->nullable();
            $table->string('accommodation_type');
            $table->string('street_address')->nullable();
            $table->string('suburb')->nullable();
            $table->string('state')->nullable();
            $table->string('post_code')->nullable();
            $table->boolean('is_looking_hm');
            $table->string('relative_name')->nullable();
            $table->foreignId('support_coordinator_id')->nullable()->constrained('support_coordinators')->onDelete('set null'); // Set null if SC is deleted
            $table->string('participant_code_name')->unique();
            $table->boolean('has_accommodation');
            $table->foreignId('added_by_user_id')->constrained('users')->onDelete('cascade'); // User who created the record
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
