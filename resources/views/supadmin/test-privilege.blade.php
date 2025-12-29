@extends('supadmin.sa-db')

@section('content')
<div class="bg-white shadow-md rounded-lg p-6 mb-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-[#33595a] flex items-center">
                <i class="fas fa-check-circle text-green-500 mr-3"></i>
                Test Privilege Access
            </h1>
            <p class="mt-2 text-[#bcbabb]">You have successfully accessed this restricted feature!</p>
        </div>
        <div class="flex items-center space-x-4">
            <a href="{{ route('superadmin.dashboard') }}" class="bg-[#cc8e45] text-white px-4 py-2 rounded-lg hover:bg-orange-600 transition duration-200 flex items-center">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Dashboard
            </a>
        </div>
    </div>
</div>

<!-- Success Message -->
<div class="bg-white rounded-lg shadow-xl p-8">
    <div class="text-center">
        <div class="w-24 h-24 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
            <i class="fas fa-check-circle text-green-500 text-3xl"></i>
        </div>
        
        <h2 class="text-2xl font-bold text-gray-900 mb-4">Access Granted!</h2>
        
        <div class="bg-green-50 border border-green-200 rounded-lg p-6 mb-6 max-w-2xl mx-auto">
            <p class="text-gray-800 text-lg mb-4">Congratulations! You have the required privilege to access this feature.</p>
            
            <div class="bg-white border border-green-200 rounded-lg p-4">
                <h3 class="text-sm font-semibold text-gray-700 mb-2">Required Privilege:</h3>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                    <i class="fas fa-shield-alt mr-2"></i>
                    Manage Backups
                </span>
            </div>
        </div>
        
        <div class="text-gray-600 mb-6">
            <p class="mb-2">This is a test page that requires the "manage_backups" privilege.</p>
            <p class="text-sm">If you can see this page, it means you have the necessary permissions.</p>
        </div>
        
        <div class="flex gap-4 justify-center">
            <a href="{{ route('superadmin.modal-demo') }}" 
               class="bg-blue-500 text-white px-6 py-3 rounded-lg hover:bg-blue-600 transition duration-200 font-medium flex items-center">
                <i class="fas fa-window mr-2"></i>
                Modal Demo
            </a>
            <a href="{{ route('superadmin.dashboard') }}" 
               class="bg-gray-500 text-white px-6 py-3 rounded-lg hover:bg-gray-600 transition duration-200 font-medium flex items-center">
                <i class="fas fa-home mr-2"></i>
                Go to Dashboard
            </a>
        </div>
    </div>
</div>

<!-- Test Links -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-8">
    <!-- Test Unauthorized Access -->
    <div class="bg-white rounded-lg shadow-lg p-6">
        <h3 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
            <i class="fas fa-lock text-amber-500 mr-3"></i>
            Test Unauthorized Access
        </h3>
        <p class="text-gray-700 mb-4">Try accessing features you don't have privileges for:</p>
        <div class="space-y-2">
            <a href="{{ route('superadmin.unauthorized') }}?message=Test unauthorized access&privilege=test_privilege&privilegeName=Test Privilege" 
               class="block bg-amber-500 text-white px-4 py-2 rounded-lg hover:bg-amber-600 transition duration-200 text-center">
                Test Unauthorized Page
            </a>
            <button onclick="showUnauthorized('Test unauthorized modal access')" 
                    class="w-full bg-amber-600 text-white px-4 py-2 rounded-lg hover:bg-amber-700 transition duration-200">
                Test Unauthorized Modal
            </button>
        </div>
    </div>
    
    <!-- Your Privileges -->
    <div class="bg-white rounded-lg shadow-lg p-6">
        <h3 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
            <i class="fas fa-list text-blue-500 mr-3"></i>
            Your Current Privileges
        </h3>
        <div class="space-y-2">
            @if(auth()->user()->privileges && count(auth()->user()->privileges) > 0)
                @foreach(auth()->user()->getReadablePrivileges() as $privilege)
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800 mr-2 mb-2">
                    <i class="fas fa-check mr-1"></i>
                    {{ $privilege }}
                </span>
                @endforeach
            @else
                <p class="text-gray-600 text-sm">No specific privileges assigned. Contact your administrator.</p>
            @endif
        </div>
    </div>
</div>
@endsection

