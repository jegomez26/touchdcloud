<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // database/migrations/xxxx_xx_xx_create_participants_table.php (your existing one)

    public function up(): void
    {
        Schema::create('participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->unique()->constrained('users')->onDelete('cascade');

            // REMOVE THESE LINES FROM HERE:
            // $table->string('first_name');
            // $table->string('last_name');

            $table->string('middle_name')->nullable();
            $table->date('birthday')->nullable();
            $table->string('disability_type')->nullable();
            $table->text('specific_disability')->nullable();
            $table->string('accommodation_type')->nullable();
            $table->string('street_address')->nullable();
            $table->string('suburb')->nullable();
            $table->string('state')->nullable();
            $table->string('post_code')->nullable();
            $table->boolean('is_looking_hm')->default(false);
            $table->string('relative_name')->nullable(); // This is a string, not an FK
            $table->foreignId('support_coordinator_id')->nullable()->constrained('support_coordinators')->onDelete('set null');

            $table->string('participant_code_name')->unique()->nullable();
            $table->boolean('has_accommodation')->default(false);

            // This is how you identify who handles the account for the participant
            $table->foreignId('representative_user_id')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null');

            $table->foreignId('added_by_user_id')->constrained('users')->onDelete('cascade');
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