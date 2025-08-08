{{-- This file, profile-panel.blade.php, defines the main panel structure. --}}
{{-- It should be placed in resources/views/indiv/ --}}

@extends('indiv.indiv-db')

@section('main-content')
<div class="panel-container flex flex-col md:flex-row bg-custom-light-cream rounded-xl shadow-smooth p-6 md:p-8 min-h-screen font-sans">
    <div class="sidebar flex-none w-full md:w-72 bg-custom-white rounded-xl shadow-md-light p-6 md:p-8 mb-6 md:mb-0 md:mr-8">
        <h2 class="text-2xl md:text-2xl font-extrabold mb-6 text-primary-dark">Participant Profile</h2>
        <nav>
            <a href="{{ route('indiv.profile.basic-details') }}"
               class="nav-link block p-3 mb-3 rounded-lg text-text-light text-base md:text-lg transition-all duration-300 ease-in-out
                      border-l-4 border-transparent hover:bg-secondary-bg hover:text-text-dark hover:border-accent-yellow
                      {{ request()->routeIs('profile.basic-details') ? 'bg-primary-dark text-custom-white font-semibold border-accent-yellow' : '' }}">
                Basic Details
            </a>
            <a href="{{ route('indiv.profile.ndis-support-needs') }}"
               class="nav-link block p-3 mb-3 rounded-lg text-text-light text-base md:text-lg transition-all duration-300 ease-in-out
                      border-l-4 border-transparent hover:bg-secondary-bg hover:text-text-dark hover:border-accent-yellow
                      {{ request()->routeIs('profile.ndis-support-needs') ? 'bg-primary-dark text-custom-white font-semibold border-accent-yellow' : '' }}">
                NDIS Details and Support Needs
            </a>
            <a href="{{ route('indiv.profile.health-safety') }}"
               class="nav-link block p-3 mb-3 rounded-lg text-text-light text-base md:text-lg transition-all duration-300 ease-in-out
                      border-l-4 border-transparent hover:bg-secondary-bg hover:text-text-dark hover:border-accent-yellow
                      {{ request()->routeIs('profile.health-safety') ? 'bg-primary-dark text-custom-white font-semibold border-accent-yellow' : '' }}">
                Health and Safety
            </a>
            <a href="{{ route('indiv.profile.living-preferences') }}"
               class="nav-link block p-3 mb-3 rounded-lg text-text-light text-base md:text-lg transition-all duration-300 ease-in-out
                      border-l-4 border-transparent hover:bg-secondary-bg hover:text-text-dark hover:border-accent-yellow
                      {{ request()->routeIs('profile.living-preferences') ? 'bg-primary-dark text-custom-white font-semibold border-accent-yellow' : '' }}">
                Living Preferences
            </a>
            <a href="{{ route('indiv.profile.compatibility-personality') }}"
               class="nav-link block p-3 mb-3 rounded-lg text-text-light text-base md:text-lg transition-all duration-300 ease-in-out
                      border-l-4 border-transparent hover:bg-secondary-bg hover:text-text-dark hover:border-accent-yellow
                      {{ request()->routeIs('profile.compatibility-personality') ? 'bg-primary-dark text-custom-white font-semibold border-accent-yellow' : '' }}">
                Compatibility and Personality
            </a>
            <a href="{{ route('indiv.profile.availability') }}"
               class="nav-link block p-3 mb-3 rounded-lg text-text-light text-base md:text-lg transition-all duration-300 ease-in-out
                      border-l-4 border-transparent hover:bg-secondary-bg hover:text-text-dark hover:border-accent-yellow
                      {{ request()->routeIs('profile.availability') ? 'bg-primary-dark text-custom-white font-semibold border-accent-yellow' : '' }}">
                Availability
            </a>
            <a href="{{ route('indiv.profile.emergency-contact') }}"
               class="nav-link block p-3 mb-3 rounded-lg text-text-light text-base md:text-lg transition-all duration-300 ease-in-out
                      border-l-4 border-transparent hover:bg-secondary-bg hover:text-text-dark hover:border-accent-yellow
                      {{ request()->routeIs('profile.emergency-contact') ? 'bg-primary-dark text-custom-white font-semibold border-accent-yellow' : '' }}">
                Emergency Contact Details
            </a>
        </nav>
    </div>

    <div class="content-area flex-1 bg-custom-white rounded-xl shadow-md-light p-6 md:p-8">
        @yield('profile-content')
    </div>
</div>
@endsection
