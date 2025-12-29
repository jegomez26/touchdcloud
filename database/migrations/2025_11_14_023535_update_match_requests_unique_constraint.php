<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Drops the unique constraint on user IDs to allow multiple participants
     * from the same provider/SC to send match requests to the same participant.
     * Duplicate checking is now handled at the application level based on participant IDs.
     */
    public function up(): void
    {
        Schema::table('match_requests', function (Blueprint $table) {
            // Drop the old unique constraint on user IDs
            // This allows multiple participants from the same provider/SC to send match requests
            $table->dropUnique('unique_user_match_request');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('match_requests', function (Blueprint $table) {
            // Restore the old constraint
            $table->unique(['sender_user_id', 'receiver_user_id'], 'unique_user_match_request');
        });
    }
};
