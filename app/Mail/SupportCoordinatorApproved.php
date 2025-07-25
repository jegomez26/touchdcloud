<?php

namespace App\Mail;

use App\Models\SupportCoordinator;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SupportCoordinatorApproved extends Mailable
{
    use Queueable, SerializesModels;

    public $coordinator;
    public $loginLink;

    /**
     * Create a new message instance.
     */
    public function __construct(SupportCoordinator $coordinator)
    {
        $this->coordinator = $coordinator;
        $this->loginLink = route('login'); // Generate login link
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your TouchdCloud Support Coordinator Account Has Been Approved!',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.support-coordinators.approved', // Will create this view
            with: [
                'coordinatorName' => $this->coordinator->first_name,
                'loginLink' => $this->loginLink,
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