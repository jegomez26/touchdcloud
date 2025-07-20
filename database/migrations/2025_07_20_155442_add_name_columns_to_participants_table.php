<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNameColumnsToParticipantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('participants', function (Blueprint $table) {
            // Add first_name, middle_name, and last_name columns
            // Assuming they are strings and nullable (adjust as per your needs)
            $table->string('first_name')->nullable()->after('added_by_user_id'); // Or after user_id, depending on where you want it
            // $table->string('middle_name')->nullable()->after('first_name');
            $table->string('last_name')->nullable()->after('middle_name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('participants', function (Blueprint $table) {
            // Drop the columns if rolling back the migration
            $table->dropColumn(['first_name', 'middle_name', 'last_name']);
        });
    }
}