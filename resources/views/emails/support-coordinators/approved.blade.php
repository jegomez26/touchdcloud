<x-mail::message>
# Welcome to TouchdCloud, {{ $coordinatorName }}!

Great news! Your Support Coordinator account for TouchdCloud has been **approved**.

You can now log in and start managing your NDIS participants, connecting with other providers, and utilizing all the features TouchdCloud has to offer.

<x-mail::button :url="$loginLink">
Login to Your Account
</x-mail::button>

We're excited to have you on board!

Thanks,<br>
The {{ config('app.name') }} Team
</x-mail::message>