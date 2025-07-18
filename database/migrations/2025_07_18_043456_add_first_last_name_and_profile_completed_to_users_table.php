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
        Schema::table('users', function (Blueprint $table) {
            // Remove the 'name' column as we're splitting it
            $table->dropColumn('name');

            // Add first_name and last_name
            $table->string('first_name')->after('id');
            $table->string('last_name')->after('first_name');

            // Add profile_completed flag, default to false
            $table->boolean('profile_completed')->default(false)->after('password');

            // Add role column if not already present
            // $table->string('role')->default('individual')->after('email'); // Uncomment if role is not yet there
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('profile_completed');
            $table->dropColumn('last_name');
            $table->dropColumn('first_name');
            // Re-add 'name' column if you roll back
            $table->string('name')->nullable()->after('id');
        });
    }
};