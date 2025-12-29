<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SupportTicket;
use App\Models\User;

class SupportTicketSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get some users to create tickets for
        $users = User::whereIn('role', ['participant', 'provider', 'support_coordinator'])->take(5)->get();
        
        if ($users->count() === 0) {
            $this->command->info('No users found to create tickets for. Skipping ticket seeding.');
            return;
        }
        
        $ticketTypes = ['bug_report', 'feature_request', 'technical_issue', 'account_issue', 'billing_question', 'general_inquiry', 'complaint'];
        $priorities = ['low', 'medium', 'high', 'urgent'];
        $statuses = ['open', 'in_progress', 'pending', 'resolved', 'closed'];
        
        $ticketData = [
            [
                'title' => 'Login issues with mobile app',
                'description' => 'I am unable to log into the mobile application. The app keeps showing "Invalid credentials" even though I am using the correct email and password.',
                'type' => 'technical_issue',
                'priority' => 'high',
                'status' => 'open'
            ],
            [
                'title' => 'Request for dark mode feature',
                'description' => 'It would be great to have a dark mode option in the application. The current white background is too bright for evening use.',
                'type' => 'feature_request',
                'priority' => 'low',
                'status' => 'pending'
            ],
            [
                'title' => 'Billing question about subscription',
                'description' => 'I was charged twice for my monthly subscription. Can you please check my account and refund the duplicate charge?',
                'type' => 'billing_question',
                'priority' => 'urgent',
                'status' => 'in_progress'
            ],
            [
                'title' => 'Profile picture not uploading',
                'description' => 'I have been trying to upload a profile picture but it keeps failing. The file is under 2MB and is a JPG format.',
                'type' => 'bug_report',
                'priority' => 'medium',
                'status' => 'resolved'
            ],
            [
                'title' => 'Account deactivated without notice',
                'description' => 'My account was deactivated without any prior notice or explanation. I need this resolved immediately as I have important work to complete.',
                'type' => 'complaint',
                'priority' => 'urgent',
                'status' => 'open'
            ],
            [
                'title' => 'How to change password',
                'description' => 'I forgot my password and need to reset it. Can you guide me through the process?',
                'type' => 'general_inquiry',
                'priority' => 'low',
                'status' => 'resolved'
            ],
            [
                'title' => 'Cannot access provider dashboard',
                'description' => 'After logging in, I cannot access my provider dashboard. I get redirected to a blank page.',
                'type' => 'account_issue',
                'priority' => 'high',
                'status' => 'in_progress'
            ],
            [
                'title' => 'Request for email notifications',
                'description' => 'It would be helpful to receive email notifications when new messages are received or when there are updates to my account.',
                'type' => 'feature_request',
                'priority' => 'medium',
                'status' => 'pending'
            ]
        ];
        
        foreach ($ticketData as $index => $data) {
            $user = $users[$index % $users->count()];
            
            $ticket = SupportTicket::create([
                'ticket_number' => SupportTicket::generateTicketNumber(),
                'title' => $data['title'],
                'description' => $data['description'],
                'type' => $data['type'],
                'priority' => $data['priority'],
                'status' => $data['status'],
                'user_id' => $user->id,
                'assigned_to' => null,
                'resolved_at' => $data['status'] === 'resolved' ? now()->subDays(rand(1, 30)) : null,
                'resolution_notes' => $data['status'] === 'resolved' ? 'Issue has been resolved successfully.' : null,
            ]);
            
            $this->command->info("Created ticket: {$ticket->ticket_number}");
        }
        
        $this->command->info('Support tickets seeded successfully!');
    }
}
