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
            // Make provider_id nullable to support support coordinators
            $table->foreignId('provider_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('participant_matches', function (Blueprint $table) {
            // Make provider_id not nullable again
            $table->foreignId('provider_id')->nullable(false)->change();
        });
    }
};