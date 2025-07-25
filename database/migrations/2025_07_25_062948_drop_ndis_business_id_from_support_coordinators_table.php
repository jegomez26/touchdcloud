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
        Schema::table('support_coordinators', function (Blueprint $table) {
            // Drop the foreign key constraint first
            // The constraint name might be 'support_coordinators_ndis_business_id_foreign'
            // or sometimes Laravel generates a different one like 'support_coordinators_abn_foreign'
            // based on what it found last. Try 'support_coordinators_ndis_business_id_foreign' first.
            // If that fails, inspect your database for the actual constraint name.
            $table->dropForeign(['abn']); // This drops the FK based on column name
            // If the error message mentioned 'abn' as the FK column, then you might need:
            // $table->dropForeign(['abn']); // Only if 'abn' itself became the foreign key

            // Now, drop the column itself (if it still exists and you want to completely remove it)
            // You mentioned it was removed from the schema, but if it exists in DB:
            $table->dropColumn('abn');

            // Ensure 'abn' is just a string without a foreign key
            // If it somehow got a foreign key, you might need:
            // $table->dropForeign('support_coordinators_abn_foreign'); // Or whatever the actual FK name is
            // And then re-add it as a plain string:
            // $table->string('abn')->nullable()->change(); // Use change() if it exists and needs modification
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('support_coordinators', function (Blueprint $table) {
            // Re-add the column and foreign key if you ever need to rollback this specific migration.
            // This rollback logic should match your previous migration exactly if you want to reverse it fully.
            // For a complete rollback and re-migration strategy, see Step 3.
            $table->foreignId('abn')->nullable()->constrained('abn')->onDelete('cascade');
            // If you intend to rollback, make sure 'company_name' and 'abn' are also reversible here.
            $table->dropColumn(['company_name', 'abn']); // Assuming you added them in a previous migration.
        });
    }
};