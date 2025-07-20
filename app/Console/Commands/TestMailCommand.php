<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log; // Required for Log::error()


class TestMailCommand extends Command
{
    protected $signature = 'mail:test {recipient}';
    protected $description = 'Send a test email to a specified recipient.';

    public function handle()
    {
        $recipient = $this->argument('recipient');

        // Create a dummy Mailable (you can just use a simple closure for quick test)
        try {
            Mail::raw('This is a test email from your Laravel app.', function ($message) use ($recipient) {
                $message->to($recipient)
                        ->subject('Laravel Test Email');
            });

            $this->info("Test email sent successfully to {$recipient}!");
        } catch (\Exception $e) {
            $this->error("Failed to send test email: " . $e->getMessage());
            // Log the full exception for more details
            Log::error("Mail Test Error: " . $e->getMessage(), ['exception' => $e]);

        }
    }
}