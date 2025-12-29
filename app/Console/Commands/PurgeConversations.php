<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\ParticipantMatch;

class PurgeConversations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:purge-conversations {--force : Run without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete all conversations and messages (irreversible)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (!$this->option('force') && !$this->confirm('This will delete ALL conversations and messages. Continue?')) {
            $this->warn('Aborted.');
            return self::SUCCESS;
        }

        $this->info('Deleting messages...');
        Message::query()->delete();

        $this->info('Deleting conversations...');
        Conversation::query()->delete();

        // Clear any foreign keys stored in participant_matches table
        if (class_exists(ParticipantMatch::class)) {
            ParticipantMatch::query()->update(['conversation_id' => null]);
        }

        $this->info('All conversations and messages removed.');
        return self::SUCCESS;
    }
}
