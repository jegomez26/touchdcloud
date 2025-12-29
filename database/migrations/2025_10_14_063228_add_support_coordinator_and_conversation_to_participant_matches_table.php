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
        Schema::table('participant_matches', function (Blueprint $table) {
            $table->unsignedBigInteger('support_coordinator_id')->nullable()->after('provider_id');
            $table->unsignedBigInteger('conversation_id')->nullable()->after('notes');
            
            $table->foreign('support_coordinator_id')->references('id')->on('support_coordinators')->onDelete('cascade');
            $table->foreign('conversation_id')->references('id')->on('conversations')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('participant_matches', function (Blueprint $table) {
            $table->dropForeign(['support_coordinator_id']);
            $table->dropForeign(['conversation_id']);
            $table->dropColumn(['support_coordinator_id', 'conversation_id']);
        });
    }
};
