@extends('supadmin.sa-db')

@section('content')
<div class="bg-white shadow-md rounded-lg p-6 mb-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-[#33595a] flex items-center">
                <i class="fas fa-file-alt text-[#cc8e45] mr-3"></i>
                System Logs
            </h1>
            <p class="mt-2 text-[#bcbabb]">View and manage system logs for debugging and monitoring.</p>
        </div>
        <div class="flex items-center space-x-4">
            <a href="{{ route('superadmin.logs.download') }}" 
               class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition duration-200 flex items-center">
                <i class="fas fa-download mr-2"></i>
                Download Logs
            </a>
            <a href="{{ route('superadmin.dashboard') }}" class="bg-[#cc8e45] text-white px-4 py-2 rounded-lg hover:bg-orange-600 transition duration-200 flex items-center">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Dashboard
            </a>
        </div>
    </div>
</div>

<!-- Log Statistics -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-lg shadow-xl p-6 text-center">
        <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-file-alt text-blue-600 text-2xl"></i>
        </div>
        <h3 class="text-2xl font-bold text-blue-600">{{ count($logContent) }}</h3>
        <p class="text-gray-600">Log Entries</p>
    </div>
    
    <div class="bg-white rounded-lg shadow-xl p-6 text-center">
        <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-exclamation-triangle text-red-600 text-2xl"></i>
        </div>
        <h3 class="text-2xl font-bold text-red-600">{{ collect($logContent)->filter(function($line) { return strpos($line, 'ERROR') !== false; })->count() }}</h3>
        <p class="text-gray-600">Errors</p>
    </div>
    
    <div class="bg-white rounded-lg shadow-xl p-6 text-center">
        <div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-exclamation-circle text-yellow-600 text-2xl"></i>
        </div>
        <h3 class="text-2xl font-bold text-yellow-600">{{ collect($logContent)->filter(function($line) { return strpos($line, 'WARNING') !== false; })->count() }}</h3>
        <p class="text-gray-600">Warnings</p>
    </div>
    
    <div class="bg-white rounded-lg shadow-xl p-6 text-center">
        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-info-circle text-green-600 text-2xl"></i>
        </div>
        <h3 class="text-2xl font-bold text-green-600">{{ collect($logContent)->filter(function($line) { return strpos($line, 'INFO') !== false; })->count() }}</h3>
        <p class="text-gray-600">Info Messages</p>
    </div>
</div>

<!-- Log Controls -->
<div class="bg-white rounded-lg shadow-xl p-6 mb-6">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-xl font-bold text-[#33595a]">Log Controls</h2>
        <div class="flex items-center space-x-4">
            <button onclick="refreshLogs()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-200 flex items-center">
                <i class="fas fa-sync-alt mr-2"></i>
                Refresh
            </button>
            <button onclick="clearLogs()" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition duration-200 flex items-center">
                <i class="fas fa-trash mr-2"></i>
                Clear Logs
            </button>
        </div>
    </div>
    
    <div class="flex items-center space-x-4">
        <div class="flex items-center space-x-2">
            <label class="text-sm font-medium text-gray-700">Filter by level:</label>
            <select id="logLevelFilter" class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-[#cc8e45] focus:border-transparent">
                <option value="all">All Levels</option>
                <option value="ERROR">Errors Only</option>
                <option value="WARNING">Warnings Only</option>
                <option value="INFO">Info Only</option>
                <option value="DEBUG">Debug Only</option>
            </select>
        </div>
        
        <div class="flex items-center space-x-2">
            <label class="text-sm font-medium text-gray-700">Search:</label>
            <input type="text" id="logSearch" placeholder="Search logs..." 
                   class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-[#cc8e45] focus:border-transparent">
        </div>
        
        <div class="flex items-center space-x-2">
            <label class="text-sm font-medium text-gray-700">Auto-refresh:</label>
            <input type="checkbox" id="autoRefresh" class="rounded border-gray-300 text-[#cc8e45] focus:ring-[#cc8e45]">
        </div>
    </div>
</div>

<!-- Log Content -->
<div class="bg-white rounded-lg shadow-xl p-6">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-xl font-bold text-[#33595a]">Log Entries</h2>
        <div class="flex items-center space-x-2">
            <span class="text-sm text-gray-500">Showing last 200 entries</span>
            <button onclick="scrollToTop()" class="text-[#cc8e45] hover:text-orange-600">
                <i class="fas fa-arrow-up"></i>
            </button>
            <button onclick="scrollToBottom()" class="text-[#cc8e45] hover:text-orange-600">
                <i class="fas fa-arrow-down"></i>
            </button>
        </div>
    </div>

    @if(count($logContent) > 0)
        <div id="logContainer" class="bg-gray-900 text-green-400 p-4 rounded-lg font-mono text-sm max-h-96 overflow-y-auto">
            @foreach($logContent as $index => $line)
                @php
                    $logLevel = strpos($line, 'ERROR') !== false ? 'ERROR' : (strpos($line, 'WARNING') !== false ? 'WARNING' : (strpos($line, 'INFO') !== false ? 'INFO' : (strpos($line, 'DEBUG') !== false ? 'DEBUG' : 'INFO')));
                @endphp
                <div class="log-entry mb-1 {{ $logLevel }}" data-level="{{ $logLevel }}">
                    <span class="text-gray-500">{{ $index + 1 }}:</span>
                    <span class="log-content">{{ $line }}</span>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-12">
            <i class="fas fa-file-alt text-gray-400 text-6xl mb-4"></i>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No log entries found</h3>
            <p class="text-gray-500">The log file is empty or doesn't exist.</p>
        </div>
    @endif
</div>

@endsection

@push('scripts')
<script>
    let autoRefreshInterval;
    
    // Log level filtering
    document.getElementById('logLevelFilter').addEventListener('change', function(e) {
        filterLogs();
    });
    
    // Search functionality
    document.getElementById('logSearch').addEventListener('input', function(e) {
        filterLogs();
    });
    
    // Auto-refresh toggle
    document.getElementById('autoRefresh').addEventListener('change', function(e) {
        if (e.target.checked) {
            startAutoRefresh();
        } else {
            stopAutoRefresh();
        }
    });
    
    function filterLogs() {
        const levelFilter = document.getElementById('logLevelFilter').value;
        const searchTerm = document.getElementById('logSearch').value.toLowerCase();
        const logEntries = document.querySelectorAll('.log-entry');
        
        logEntries.forEach(entry => {
            const level = entry.dataset.level;
            const content = entry.textContent.toLowerCase();
            
            let showEntry = true;
            
            // Filter by level
            if (levelFilter !== 'all' && level !== levelFilter) {
                showEntry = false;
            }
            
            // Filter by search term
            if (searchTerm && !content.includes(searchTerm)) {
                showEntry = false;
            }
            
            entry.style.display = showEntry ? 'block' : 'none';
        });
    }
    
    function refreshLogs() {
        window.location.reload();
    }
    
    function clearLogs() {
        if (confirm('Are you sure you want to clear all logs? This action cannot be undone.')) {
            // This would typically make an AJAX request to clear logs
            alert('Log clearing functionality would be implemented here');
        }
    }
    
    function startAutoRefresh() {
        autoRefreshInterval = setInterval(refreshLogs, 30000); // Refresh every 30 seconds
    }
    
    function stopAutoRefresh() {
        if (autoRefreshInterval) {
            clearInterval(autoRefreshInterval);
        }
    }
    
    function scrollToTop() {
        document.getElementById('logContainer').scrollTop = 0;
    }
    
    function scrollToBottom() {
        const container = document.getElementById('logContainer');
        container.scrollTop = container.scrollHeight;
    }
    
    // Auto-scroll to bottom on page load
    window.addEventListener('load', function() {
        scrollToBottom();
    });
</script>

<style>
    .log-entry.ERROR {
        color: #ef4444;
    }
    
    .log-entry.WARNING {
        color: #f59e0b;
    }
    
    .log-entry.INFO {
        color: #10b981;
    }
    
    .log-entry.DEBUG {
        color: #6b7280;
    }
    
    .log-entry:hover {
        background-color: rgba(255, 255, 255, 0.1);
    }
</style>
@endpush
