<x-mail::message>
# Update Regarding Your TouchdCloud Support Coordinator Application, {{ $coordinatorName }}

Thank you for your interest in joining TouchdCloud as a Support Coordinator.

After reviewing your application, we regret to inform you that it has not been approved at this time.

**Reason for Rejection:**
"{{ $rejectionReason }}"

If you believe this is an error, or if you wish to amend your application and resubmit, please review the reason provided and click the link below to start a new application.

<x-mail::button :url="$resubmitLink">
Resubmit Your Application
</x-mail::button>

We appreciate your understanding.

Thanks,<br>
The {{ config('app.name') }} Team
</x-mail::message>