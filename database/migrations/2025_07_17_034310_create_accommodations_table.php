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
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('provider_id')->constrained('providers')->onDelete('cascade');
            $table->string('title');
            $table->text('description');
            $table->string('type');
            $table->string('address');
            $table->string('suburb');
            $table->string('state');
            $table->string('post_code');
            $table->integer('num_bedrooms');
            $table->integer('num_bathrooms');
            $table->decimal('rent_per_week', 10, 2);
            $table->boolean('is_available_for_hm');
            $table->json('amenities')->nullable(); // Store as JSON string
            $table->json('photos')->nullable();   // Store as JSON string of paths
            $table->enum('status', ['available', 'occupied', 'draft', 'archived'])->default('draft');
            $table->integer('total_vacancies');
            $table->integer('current_occupancy')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
