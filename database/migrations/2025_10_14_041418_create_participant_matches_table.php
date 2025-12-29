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
        Schema::create('participant_matches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('provider_id')->constrained()->onDelete('cascade');
            $table->foreignId('seeking_participant_id')->constrained('participants')->onDelete('cascade');
            $table->foreignId('matched_participant_id')->constrained('participants')->onDelete('cascade');
            $table->integer('compatibility_score')->default(0);
            $table->json('compatibility_factors')->nullable();
            $table->json('match_details')->nullable(); // Store additional match analysis
            $table->enum('status', ['active', 'contacted', 'interested', 'not_interested', 'matched'])->default('active');
            $table->timestamp('last_viewed_at')->nullable();
            $table->timestamp('contacted_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // Ensure unique matches per provider
            $table->unique(['provider_id', 'seeking_participant_id', 'matched_participant_id'], 'unique_provider_match');
            
            // Indexes for performance
            $table->index(['provider_id', 'seeking_participant_id']);
            $table->index(['provider_id', 'status']);
            $table->index('compatibility_score');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('participant_matches');
    }
};