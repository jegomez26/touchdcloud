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
        Schema::create('property_participant', function (Blueprint $table) {
            // No 'id' column here for a simple pivot table
            $table->foreignId('property_id')->constrained('properties')->onDelete('cascade');
            $table->foreignId('participant_id')->constrained('participants')->onDelete('cascade');
            $table->primary(['property_id', 'participant_id']); // Composite primary key
            $table->date('assignment_date');
            $table->date('end_date')->nullable();
            $table->boolean('is_current_resident')->default(true);
            $table->foreignId('assigned_by_user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('property_participant');
    }
};
