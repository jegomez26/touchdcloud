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
            // Update the enum to include participant_to_participant
            $table->enum('type', ['sc_to_participant', 'provider_to_sc', 'internal_sc_notes', 'participant_to_participant'])
                  ->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('conversations', function (Blueprint $table) {
            // Revert to original enum values
            $table->enum('type', ['sc_to_participant', 'provider_to_sc', 'internal_sc_notes'])
                  ->change();
        });
    }
};