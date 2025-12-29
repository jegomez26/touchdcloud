<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Conversation;
use App\Models\Message;

class CleanupConversationConflicts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'conversations:cleanup {--dry-run : Show what would be deleted without actually deleting}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up conflicting or problematic conversations';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $isDryRun = $this->option('dry-run');
        
        if ($isDryRun) {
            $this->info('🔍 DRY RUN MODE - No changes will be made');
        }

        $this->info('🧹 Starting conversation cleanup...');

        $conflicts = [];

        // 1. Check for conversations with missing required relationships
        $this->info('1. Checking for conversations with missing relationships...');
        
        $invalidParticipant = Conversation::whereDoesntHave('participant')->get();
        $this->line("   - Conversations with invalid participant_id: {$invalidParticipant->count()}");
        $conflicts = array_merge($conflicts, $invalidParticipant->pluck('id')->toArray());

        $invalidSC = Conversation::whereNotNull('support_coordinator_id')
            ->whereDoesntHave('supportCoordinator')->get();
        $this->line("   - Conversations with invalid support_coordinator_id: {$invalidSC->count()}");
        $conflicts = array_merge($conflicts, $invalidSC->pluck('id')->toArray());

        $invalidProvider = Conversation::whereNotNull('provider_id')
            ->whereDoesntHave('provider')->get();
        $this->line("   - Conversations with invalid provider_id: {$invalidProvider->count()}");
        $conflicts = array_merge($conflicts, $invalidProvider->pluck('id')->toArray());

        $invalidMatching = Conversation::whereNotNull('matching_for_participant_id')
            ->whereDoesntHave('matchingForParticipant')->get();
        $this->line("   - Conversations with invalid matching_for_participant_id: {$invalidMatching->count()}");
        $conflicts = array_merge($conflicts, $invalidMatching->pluck('id')->toArray());

        // 2. Check for logical inconsistencies
        $this->info('2. Checking for logical inconsistencies...');
        
        $inconsistent = Conversation::where(function($query) {
            $query->where('type', 'sc_to_participant')
                  ->whereNull('support_coordinator_id');
        })->orWhere(function($query) {
            $query->where('type', 'provider_to_sc')
                  ->whereNull('provider_id');
        })->orWhere(function($query) {
            $query->where('type', 'provider_to_participant')
                  ->whereNull('provider_id');
        })->get();

        $this->line("   - Conversations with inconsistent type/relationship data: {$inconsistent->count()}");
        $conflicts = array_merge($conflicts, $inconsistent->pluck('id')->toArray());

        // 3. Check for self-matching conversations
        $selfMatching = Conversation::whereColumn('participant_id', 'matching_for_participant_id')->get();
        $this->line("   - Conversations where participant matches themselves: {$selfMatching->count()}");
        $conflicts = array_merge($conflicts, $selfMatching->pluck('id')->toArray());

        // 4. Check for orphaned conversations (no messages)
        $this->info('3. Checking for orphaned conversations...');
        $orphaned = Conversation::whereDoesntHave('messages')->get();
        $this->line("   - Conversations without messages: {$orphaned->count()}");
        $conflicts = array_merge($conflicts, $orphaned->pluck('id')->toArray());

        // Remove duplicates
        $conflicts = array_unique($conflicts);

        $this->info("\n📊 SUMMARY:");
        $this->line("Total conflicting conversations: " . count($conflicts));

        if (count($conflicts) > 0) {
            $this->warn("Conflicting conversation IDs: " . implode(', ', $conflicts));
            
            if (!$isDryRun) {
                $this->info("\n🗑️  Removing conflicts...");
                
                // Delete messages first
                $messagesDeleted = Message::whereIn('conversation_id', $conflicts)->delete();
                $this->line("   - Deleted {$messagesDeleted} messages");
                
                // Delete conversations
                $conversationsDeleted = Conversation::whereIn('id', $conflicts)->delete();
                $this->line("   - Deleted {$conversationsDeleted} conversations");
                
                $this->info("\n✅ Cleanup completed successfully!");
            } else {
                $this->info("\n🔍 DRY RUN: Would delete " . count($conflicts) . " conversations");
            }
        } else {
            $this->info("\n✅ No conflicts found!");
        }

        $this->info("\n📈 FINAL STATUS:");
        $this->line("Remaining conversations: " . Conversation::count());
        $this->line("Remaining messages: " . Message::count());

        return 0;
    }
}
