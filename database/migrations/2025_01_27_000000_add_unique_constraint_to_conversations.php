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
        Schema::table('conversations', function (Blueprint $table) {
            // Add unique constraint to prevent duplicate conversations
            $table->unique([
                'type', 
                'participant_id', 
                'support_coordinator_id', 
                'provider_id', 
                'matching_for_participant_id'
            ], 'conversations_unique_constraint');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('conversations', function (Blueprint $table) {
            $table->dropUnique('conversations_unique_constraint');
        });
    }
};
