@extends('layouts.app') {{-- Or your guest layout if you have one specific for public pages --}}

@section('content')
<div class="container mx-auto px-4 py-8 max-w-3xl bg-custom-white rounded-lg shadow-xl mt-10 mb-10">
    <h1 class="text-3xl font-extrabold text-custom-black mb-6 text-center">Privacy Policy</h1>

    <div class="prose max-w-none text-custom-dark-olive">
        <h2>1. Introduction</h2>
        <p>Your privacy is important to us. This Privacy Policy explains how TouchdCloud collects, uses, discloses, and safeguards your information when you visit our website <a href="http://www.touchdcloud.com" class="underline text-custom-dark-teal hover:text-custom-ochre">www.touchdcloud.com</a>, including any other media form, media channel, mobile website, or mobile application related or connected thereto (collectively, the “Site”). Please read this privacy policy carefully. If you do not agree with the terms of this privacy policy, please do not access the Site.</p>

        <h2>2. Information We Collect</h2>
        <p>We may collect personal information that you voluntarily provide to us when you register on the Site, express an interest in obtaining information about us or our products and services, when you participate in activities on the Site, or otherwise when you contact us.</p>
        <p>The personal information that we collect depends on the context of your interactions with us and the Site, the choices you make and the products and features you use. The personal information we collect may include the following:</p>
        <ul>
            <li>Names</li>
            <li>Email addresses</li>
            <li>Contact preferences</li>
            <li>Information related to NDIS participant needs or accommodation provider details</li>
        </ul>

        <h2>3. How We Use Your Information</h2>
        <p>We use personal information collected via our Site for a variety of business purposes described below. We process your personal information for these purposes in reliance on our legitimate business interests, in order to enter into or perform a contract with you, with your consent, and/or for compliance with our legal obligations. We indicate the specific processing grounds we rely on next to each purpose listed below:</p>
        <ul>
            <li>To facilitate account creation and logon process.</li>
            <li>To post testimonials with your consent.</li>
            <li>To send you marketing and promotional communications.</li>
            <li>To respond to your inquiries and offer support.</li>
            <li>To fulfill and manage your orders related to the Site.</li>
            <li>To enable user-to-user communications with your consent.</li>
        </ul>

        <h2>4. Disclosure of Your Information</h2>
        <p>We may share information we have collected about you in certain situations. Your information may be disclosed as follows:</p>
        <ul>
            <li>**By Law or to Protect Rights:** If we believe the release of information about you is necessary to respond to legal process, to investigate or remedy potential violations of our policies, or to protect the rights, property, or safety of others, we may share your information as permitted or required by any applicable law, rule, or regulation.</li>
            <li>**Third-Party Service Providers:** We may share your information with third parties that perform services for us or on our behalf, including data analysis, email delivery, hosting services, customer service, and marketing assistance.</li>
            <li>**Business Transfers:** We may share or transfer your information in connection with, or during negotiations of, any merger, sale of company assets, financing, or acquisition of all or a portion of our business to another company.</li>
        </ul>

        <h2>5. Security of Your Information</h2>
        <p>We use administrative, technical, and physical security measures to help protect your personal information. While we have taken reasonable steps to secure the personal information you provide to us, please be aware that despite our efforts, no security measures are perfect or impenetrable, and no method of data transmission can be guaranteed against any interception or other type of misuse.</p>

        <h2>6. Policy for Children</h2>
        <p>We do not knowingly solicit information from or market to children under the age of 13. If you become aware of any data we have collected from children under age 13, please contact us using the contact information provided below.</p>

        <h2>7. Changes to This Privacy Policy</h2>
        <p>We may update this Privacy Policy from time to time. The updated version will be indicated by an updated "Revised" date and the updated version will be effective as soon as it is accessible. We encourage you to review this privacy policy frequently to be informed of how we are protecting your information.</p>

        <h2>8. Contact Us</h2>
        <p>If you have questions or comments about this Privacy Policy, please contact us at:</p>
        <p>TouchdCloud Support<br>support@touchdcloud.com</p>
    </div>

    <div class="mt-8 text-center">
        <a href="{{ url()->previous() }}" class="inline-flex items-center px-4 py-2 bg-custom-ochre border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-custom-ochre-darker focus:bg-custom-ochre-darker active:bg-custom-dark-teal focus:outline-none focus:ring-2 focus:ring-custom-ochre focus:ring-offset-2 transition ease-in-out duration-150">
            Back
        </a>
    </div>
</div>
@endsection