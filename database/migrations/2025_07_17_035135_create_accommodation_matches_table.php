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
        Schema::create('accommodation_matches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('participant_id')->constrained('participants')->onDelete('cascade');
            $table->foreignId('accommodation_id')->constrained('accommodations')->onDelete('cascade');
            $table->foreignId('support_coordinator_id')->nullable()->constrained('support_coordinators')->onDelete('set null');
            $table->foreignId('provider_id')->constrained('providers')->onDelete('cascade');
            $table->enum('match_status', ['proposed_by_provider', 'reviewed_by_sc', 'interest_expressed_by_participant', 'declined', 'successful'])->default('proposed_by_provider');
            $table->text('notes')->nullable();
            $table->timestamp('proposed_at')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accommodation_matches');
    }
};
