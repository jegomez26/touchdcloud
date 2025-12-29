<div class="max-w-3xl mx-auto p-6">
    <h2 class="text-2xl font-semibold text-gray-800 mb-4">Match Requests</h2>
    <div id="requests-list" class="space-y-3"></div>

    <div id="empty-state" class="hidden text-center text-gray-500 py-8">No pending requests.</div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    fetch('/match-requests/pending', { headers: { 'Accept': 'application/json' }})
        .then(r => r.json())
        .then(data => {
            const list = document.getElementById('requests-list');
            list.innerHTML = '';
            if (!data.success || !data.requests || data.requests.length === 0) {
                document.getElementById('empty-state').classList.remove('hidden');
                return;
            }
            data.requests.forEach(req => {
                const row = document.createElement('div');
                row.className = 'bg-white rounded-md border p-4 flex items-center justify-between';
                const senderName = (req.sender_user && (req.sender_user.first_name || req.sender_user.last_name)) ? `${req.sender_user.first_name ?? ''} ${req.sender_user.last_name ?? ''}`.trim() : `User #${req.sender_user_id}`;
                row.innerHTML = `
                    <div>
                        <div class="font-medium text-gray-900">${senderName}</div>
                        <div class="text-sm text-gray-500">${req.message ?? ''}</div>
                    </div>
                    <div class="flex gap-2">
                        <button class="px-3 py-1 rounded-md bg-green-600 text-white hover:bg-green-700" onclick="acceptReq(${req.id})">Accept</button>
                        <button class="px-3 py-1 rounded-md bg-red-600 text-white hover:bg-red-700" onclick="rejectReq(${req.id})">Reject</button>
                    </div>
                `;
                list.appendChild(row);
            });
        });
});

function acceptReq(id){
    fetch(`/match-requests/${id}/accept`, {method:'POST', headers:{'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,'Accept':'application/json'}})
        .then(r=>r.json()).then(()=>location.reload());
}
function rejectReq(id){
    fetch(`/match-requests/${id}/reject`, {method:'POST', headers:{'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,'Accept':'application/json'}})
        .then(r=>r.json()).then(()=>location.reload());
}
</script>
@endpush


