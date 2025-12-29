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
        Schema::create('match_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sender_user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('receiver_user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('sender_participant_id')->nullable()->constrained('participants')->onDelete('cascade');
            $table->foreignId('receiver_participant_id')->nullable()->constrained('participants')->onDelete('cascade');
            $table->enum('status', ['pending', 'accepted', 'rejected'])->default('pending');
            $table->text('message')->nullable(); // Optional message with the request
            $table->timestamp('responded_at')->nullable();
            $table->timestamps();
            
            // Ensure unique requests between the same users
            $table->unique(['sender_user_id', 'receiver_user_id'], 'unique_user_match_request');
            
            // Indexes for performance
            $table->index(['receiver_user_id', 'status']);
            $table->index(['sender_user_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('match_requests');
    }
};
