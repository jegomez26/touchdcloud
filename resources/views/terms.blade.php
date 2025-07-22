@extends('layouts.app') {{-- Or your guest layout if you have one specific for public pages --}}

@section('content')
<div class="container mx-auto px-4 py-8 max-w-3xl bg-custom-white rounded-lg shadow-xl mt-10 mb-10">
    <h1 class="text-3xl font-extrabold text-custom-black mb-6 text-center">Terms of Service</h1>

    <div class="prose max-w-none text-custom-dark-olive">
        <h2>1. Acceptance of Terms</h2>
        <p>By accessing or using the TouchdCloud website and services, you agree to be bound by these Terms of Service ("Terms"). If you do not agree to all the terms and conditions of this agreement, then you may not access the website or use any services.</p>

        <h2>2. Services Provided</h2>
        <p>TouchdCloud provides a platform for NDIS Participants, Support Coordinators, and Accommodation Providers to connect and facilitate housing solutions.</p>

        <h2>3. User Responsibilities</h2>
        <p>Users are responsible for maintaining the confidentiality of their account and password and for restricting access to their computer. You agree to accept responsibility for all activities that occur under your account or password.</p>

        <h2>4. Content and Conduct</h2>
        <p>You agree not to post, transmit, or otherwise make available any content that is unlawful, harmful, threatening, abusive, harassing, defamatory, vulgar, obscene, libelous, invasive of another's privacy, hateful, or racially, ethnically or otherwise objectionable.</p>

        <h2>5. Disclaimers and Limitation of Liability</h2>
        <p>The services are provided "as is" without warranty of any kind. In no event shall TouchdCloud be liable for any direct, indirect, incidental, special, consequential, or exemplary damages, including but not limited to, damages for loss of profits, goodwill, use, data, or other intangible losses.</p>

        <h2>6. Changes to Terms</h2>
        <p>We reserve the right to modify these Terms at any time. We will notify you of any changes by posting the new Terms on this page.</p>

        <h2>7. Contact Information</h2>
        <p>If you have any questions about these Terms, please contact us at support@touchdcloud.com.</p>
    </div>

    <div class="mt-8 text-center">
        <a href="{{ url()->previous() }}" class="inline-flex items-center px-4 py-2 bg-custom-ochre border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-custom-ochre-darker focus:bg-custom-ochre-darker active:bg-custom-dark-teal focus:outline-none focus:ring-2 focus:ring-custom-ochre focus:ring-offset-2 transition ease-in-out duration-150">
            Back
        </a>
    </div>
</div>
@endsection