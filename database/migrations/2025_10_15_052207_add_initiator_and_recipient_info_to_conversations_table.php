<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('conversations', function (Blueprint $table) {
            // Add fields to track initiator and recipient information (only if they don't exist)
            if (!Schema::hasColumn('conversations', 'initiator_user_id')) {
                $table->unsignedBigInteger('initiator_user_id')->nullable()->after('provider_id');
            }
            if (!Schema::hasColumn('conversations', 'recipient_user_id')) {
                $table->unsignedBigInteger('recipient_user_id')->nullable()->after('initiator_user_id');
            }
            if (!Schema::hasColumn('conversations', 'initiator_participant_id')) {
                $table->unsignedBigInteger('initiator_participant_id')->nullable()->after('recipient_user_id');
            }
            if (!Schema::hasColumn('conversations', 'recipient_participant_id')) {
                $table->unsignedBigInteger('recipient_participant_id')->nullable()->after('initiator_participant_id');
            }
        });

        // Add foreign key constraints separately
        Schema::table('conversations', function (Blueprint $table) {
            if (Schema::hasColumn('conversations', 'initiator_user_id') && !$this->foreignKeyExists('conversations', 'conversations_initiator_user_id_foreign')) {
                $table->foreign('initiator_user_id')->references('id')->on('users')->onDelete('set null');
            }
            if (Schema::hasColumn('conversations', 'recipient_user_id') && !$this->foreignKeyExists('conversations', 'conversations_recipient_user_id_foreign')) {
                $table->foreign('recipient_user_id')->references('id')->on('users')->onDelete('set null');
            }
            if (Schema::hasColumn('conversations', 'initiator_participant_id') && !$this->foreignKeyExists('conversations', 'conversations_initiator_participant_id_foreign')) {
                $table->foreign('initiator_participant_id')->references('id')->on('participants')->onDelete('set null');
            }
            if (Schema::hasColumn('conversations', 'recipient_participant_id') && !$this->foreignKeyExists('conversations', 'conversations_recipient_participant_id_foreign')) {
                $table->foreign('recipient_participant_id')->references('id')->on('participants')->onDelete('set null');
            }
        });

        // Add indexes for performance
        Schema::table('conversations', function (Blueprint $table) {
            if (!$this->indexExists('conversations', 'conv_init_recip_users_idx')) {
                $table->index(['initiator_user_id', 'recipient_user_id'], 'conv_init_recip_users_idx');
            }
            if (!$this->indexExists('conversations', 'conv_init_recip_parts_idx')) {
                $table->index(['initiator_participant_id', 'recipient_participant_id'], 'conv_init_recip_parts_idx');
            }
        });
    }

    private function foreignKeyExists($table, $key)
    {
        $foreignKeys = DB::select("
            SELECT CONSTRAINT_NAME 
            FROM information_schema.KEY_COLUMN_USAGE 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = ? 
            AND CONSTRAINT_NAME = ?
        ", [$table, $key]);
        
        return count($foreignKeys) > 0;
    }

    private function indexExists($table, $index)
    {
        $indexes = DB::select("
            SELECT INDEX_NAME 
            FROM information_schema.STATISTICS 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = ? 
            AND INDEX_NAME = ?
        ", [$table, $index]);
        
        return count($indexes) > 0;
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('conversations', function (Blueprint $table) {
            $table->dropForeign(['initiator_user_id']);
            $table->dropForeign(['recipient_user_id']);
            $table->dropForeign(['initiator_participant_id']);
            $table->dropForeign(['recipient_participant_id']);
            
            $table->dropIndex('conv_init_recip_users_idx');
            $table->dropIndex('conv_init_recip_parts_idx');
            
            $table->dropColumn([
                'initiator_user_id',
                'recipient_user_id', 
                'initiator_participant_id',
                'recipient_participant_id'
            ]);
        });
    }
};