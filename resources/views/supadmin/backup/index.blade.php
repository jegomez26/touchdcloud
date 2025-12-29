@extends('supadmin.sa-db')

@section('content')
<div class="bg-white shadow-md rounded-lg p-6 mb-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-[#33595a] flex items-center">
                <i class="fas fa-database text-[#cc8e45] mr-3"></i>
                Data Backup Management
            </h1>
            <p class="mt-2 text-[#bcbabb]">Create, download, and manage system backups for data protection.</p>
        </div>
        <div class="flex items-center space-x-4">
            <form action="{{ route('superadmin.backup.create') }}" method="POST" class="inline">
                @csrf
                <button type="submit" 
                        class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition duration-200 flex items-center"
                        onclick="return confirm('This will create a new backup. Continue?')">
                    <i class="fas fa-plus mr-2"></i>
                    Create Backup
                </button>
            </form>
            <a href="{{ route('superadmin.dashboard') }}" class="bg-[#cc8e45] text-white px-4 py-2 rounded-lg hover:bg-orange-600 transition duration-200 flex items-center">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Dashboard
            </a>
        </div>
    </div>
</div>

<!-- Backup Statistics -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-lg shadow-xl p-6 text-center">
        <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-database text-blue-600 text-2xl"></i>
        </div>
        <h3 class="text-2xl font-bold text-blue-600">{{ $backups->count() }}</h3>
        <p class="text-gray-600">Total Backups</p>
    </div>
    
    <div class="bg-white rounded-lg shadow-xl p-6 text-center">
        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-calendar-day text-green-600 text-2xl"></i>
        </div>
        <h3 class="text-2xl font-bold text-green-600">{{ $backups->where('last_modified', '>=', now()->subDays(1)->timestamp)->count() }}</h3>
        <p class="text-gray-600">Recent (24h)</p>
    </div>
    
    <div class="bg-white rounded-lg shadow-xl p-6 text-center">
        <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-hdd text-purple-600 text-2xl"></i>
        </div>
        <h3 class="text-2xl font-bold text-purple-600">{{ number_format($backups->sum('size') / 1024 / 1024, 2) }} MB</h3>
        <p class="text-gray-600">Total Size</p>
    </div>
    
    <div class="bg-white rounded-lg shadow-xl p-6 text-center">
        <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-clock text-orange-600 text-2xl"></i>
        </div>
        <h3 class="text-2xl font-bold text-orange-600">{{ $backups->isNotEmpty() ? $backups->first()['name'] : 'None' }}</h3>
        <p class="text-gray-600">Latest Backup</p>
    </div>
</div>

<!-- Backup Actions -->
<div class="bg-white rounded-lg shadow-xl p-6 mb-6">
    <h2 class="text-xl font-bold text-[#33595a] mb-4">Backup Actions</h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
            <div class="flex items-center">
                <i class="fas fa-plus-circle text-green-600 text-2xl mr-3"></i>
                <div>
                    <h3 class="font-semibold text-green-800">Create Backup</h3>
                    <p class="text-sm text-green-600">Generate a new system backup</p>
                </div>
            </div>
        </div>
        
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex items-center">
                <i class="fas fa-download text-blue-600 text-2xl mr-3"></i>
                <div>
                    <h3 class="font-semibold text-blue-800">Download Backup</h3>
                    <p class="text-sm text-blue-600">Download existing backups</p>
                </div>
            </div>
        </div>
        
        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
            <div class="flex items-center">
                <i class="fas fa-trash text-red-600 text-2xl mr-3"></i>
                <div>
                    <h3 class="font-semibold text-red-800">Delete Backup</h3>
                    <p class="text-sm text-red-600">Remove old backup files</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Backup List -->
<div class="bg-white rounded-lg shadow-xl p-6">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-bold text-[#33595a]">Available Backups</h2>
        <div class="flex items-center space-x-4">
            <div class="relative">
                <input type="text" id="searchBackups" placeholder="Search backups..." 
                       class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#cc8e45] focus:border-transparent">
                <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
            </div>
        </div>
    </div>

    @if($backups->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full table-auto">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Backup Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Size</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($backups as $backup)
                        <tr class="hover:bg-gray-50 transition-colors duration-200">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-[#f8f1e1] rounded-full flex items-center justify-center">
                                        <i class="fas fa-file-archive text-[#cc8e45]"></i>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $backup['name'] }}</div>
                                        <div class="text-sm text-gray-500">{{ $backup['path'] }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if(str_contains($backup['name'], '.zip')) bg-blue-100 text-blue-800
                                    @elseif(str_contains($backup['name'], '.sql')) bg-green-100 text-green-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    <i class="fas 
                                        @if(str_contains($backup['name'], '.zip')) fa-file-archive
                                        @elseif(str_contains($backup['name'], '.sql')) fa-database
                                        @else fa-file
                                        @endif mr-1"></i>
                                    {{ str_contains($backup['name'], '.zip') ? 'ZIP Archive' : (str_contains($backup['name'], '.sql') ? 'SQL Dump' : 'File') }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ number_format($backup['size'] / 1024, 2) }} KB
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ \Carbon\Carbon::createFromTimestamp($backup['last_modified'])->format('M d, Y H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-check-circle mr-1"></i>
                                    Available
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('superadmin.backup.download', $backup['name']) }}" 
                                       class="text-blue-600 hover:text-blue-900 transition-colors duration-200"
                                       title="Download">
                                        <i class="fas fa-download"></i>
                                    </a>
                                    
                                    <form action="{{ route('superadmin.backup.delete', $backup['name']) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="text-red-600 hover:text-red-900 transition-colors duration-200"
                                                onclick="return confirm('Are you sure you want to delete this backup?')"
                                                title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                    
                                    <button class="text-green-600 hover:text-green-900 transition-colors duration-200" 
                                            onclick="showBackupInfo('{{ $backup['name'] }}')"
                                            title="Info">
                                        <i class="fas fa-info-circle"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="text-center py-12">
            <i class="fas fa-database text-gray-400 text-6xl mb-4"></i>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No backups found</h3>
            <p class="text-gray-500">Create your first backup to get started.</p>
            <form action="{{ route('superadmin.backup.create') }}" method="POST" class="mt-4">
                @csrf
                <button type="submit" 
                        class="bg-[#cc8e45] text-white px-6 py-3 rounded-lg hover:bg-orange-600 transition duration-200">
                    <i class="fas fa-plus mr-2"></i>
                    Create First Backup
                </button>
            </form>
        </div>
    @endif
</div>

<!-- Backup Info Modal -->
<div id="backupInfoModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
            <div class="flex items-center justify-between p-6 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Backup Information</h3>
                <button onclick="closeBackupInfo()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div id="backupInfoContent" class="p-6">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // Search functionality
    document.getElementById('searchBackups').addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        const rows = document.querySelectorAll('tbody tr');
        
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            if (text.includes(searchTerm)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });

    // Backup info modal
    function showBackupInfo(backupName) {
        document.getElementById('backupInfoContent').innerHTML = `
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Backup Name</label>
                    <p class="mt-1 text-sm text-gray-900">${backupName}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Type</label>
                    <p class="mt-1 text-sm text-gray-900">${backupName.includes('.zip') ? 'ZIP Archive' : 'SQL Dump'}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Status</label>
                    <p class="mt-1 text-sm text-green-600">Available for download</p>
                </div>
                <div class="pt-4 border-t border-gray-200">
                    <p class="text-sm text-gray-500">This backup contains all system data and can be used to restore the application.</p>
                </div>
            </div>
        `;
        document.getElementById('backupInfoModal').classList.remove('hidden');
    }

    function closeBackupInfo() {
        document.getElementById('backupInfoModal').classList.add('hidden');
    }

    // Close modal when clicking outside
    document.getElementById('backupInfoModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeBackupInfo();
        }
    });
</script>
@endpush
