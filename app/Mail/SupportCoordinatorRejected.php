<?php

namespace App\Mail;

use App\Models\SupportCoordinator;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SupportCoordinatorRejected extends Mailable
{
    use Queueable, SerializesModels;

    public $coordinator;
    public $rejectionReason;
    public $resubmitLink; // If you have a specific resubmission page

    /**
     * Create a new message instance.
     */
    public function __construct(SupportCoordinator $coordinator, string $rejectionReason)
    {
        $this->coordinator = $coordinator;
        $this->rejectionReason = $rejectionReason;
        // You might want a specific route for resubmission or just direct to login with instruction.
        // For now, let's assume they can resubmit by going to the registration form again.
        $this->resubmitLink = route('register.coordinator.create');
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Update Regarding Your SIL Match Support Coordinator Application',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.support-coordinators.rejected', // Will create this view
            with: [
                'coordinatorName' => $this->coordinator->first_name,
                'rejectionReason' => $this->rejectionReason,
                'resubmitLink' => $this->resubmitLink,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}