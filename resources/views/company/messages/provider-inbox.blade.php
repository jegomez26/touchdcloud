@extends('company.provider-db')

@section('main-content')
<div class="h-[calc(100vh-180px)] grid grid-cols-1 lg:grid-cols-3 gap-4">
    <!-- Left: Conversations list -->
    <div class="bg-white rounded-lg border border-[#e1e7dd] overflow-hidden flex flex-col">
        <div class="px-4 py-3 border-b border-[#e1e7dd] text-[#3e4732] font-semibold">Inbox</div>
        <div id="conv-list" class="flex-1 overflow-y-auto divide-y">
            @forelse($conversations as $conversation)
                @php $last = $conversation->messages->first(); @endphp
                <button data-conv-id="{{ $conversation->id }}" class="w-full text-left px-4 py-3 hover:bg-[#f8f1e1] focus:bg-[#f8f1e1]">
                    @php
                        $firstMsg = $conversation->messages()->oldest()->first();
                        $isFirstReceiver = $firstMsg && $firstMsg->receiver_id === auth()->id();
                        $displayName = $isFirstReceiver ? ($conversation->participant?->first_name . ' ' . $conversation->participant?->last_name) : ($conversation->participant?->participant_code_name ?? 'Unknown');
                        $senderCode = '';
                        if ($firstMsg) {
                            if ($firstMsg->original_sender_role === 'coordinator') {
                                $senderCode = $conversation->supportCoordinator?->sup_coor_code_name;
                            } elseif ($firstMsg->original_sender_role === 'provider') {
                                $senderCode = $conversation->provider?->provider_code_name;
                            } else {
                                $senderCode = $conversation->participant?->participant_code_name;
                            }
                        }
                    @endphp
                    <div class="font-semibold text-[#3e4732]">{{ trim($displayName) ?: 'Unknown' }}</div>
                    @if($senderCode)
                        <div class="text-xs text-[#bcbabb]">From: {{ $senderCode }}</div>
                    @endif
                    <div class="text-xs text-[#bcbabb] mt-1 flex items-center justify-between">
                        <span class="truncate max-w-[75%]">{{ $last?->content }}</span>
                        <span>{{ $conversation->last_message_at?->diffForHumans() }}</span>
                    </div>
                </button>
            @empty
                <div class="p-6 text-center text-[#bcbabb]">No conversations yet</div>
            @endforelse
        </div>
        <div class="px-4 py-2 border-t border-[#e1e7dd]">{{ $conversations->links() }}</div>
    </div>

    <!-- Right: Conversation pane -->
    <div class="lg:col-span-2 bg-white rounded-lg border border-[#e1e7dd] overflow-hidden flex flex-col">
        <div id="conv-header" class="px-4 py-3 border-b border-[#e1e7dd] text-[#3e4732] font-semibold">Select a conversation</div>
        <div id="conv-body" class="flex-1 overflow-y-auto p-4 space-y-3"></div>
        <div class="px-4 py-3 border-t border-[#e1e7dd] flex items-center space-x-2">
            <textarea id="reply-text" class="flex-1 border border-[#e1e7dd] rounded-md p-2 focus:outline-none focus:ring-2 focus:ring-[#cc8e45]" rows="2" placeholder="Type a message..." disabled></textarea>
            <button id="reply-send" class="px-4 py-2 rounded-md bg-[#33595a] text-white hover:bg-[#2C494A] disabled:opacity-50" disabled>Send</button>
        </div>
    </div>

    <!-- Success/Error Modals -->
    <div id="msg-success" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-xl p-6 border border-[#e1e7dd]">
            <div class="text-[#3e4732] font-semibold mb-2">Message sent</div>
            <div class="text-[#6b7280] mb-4">Your message has been delivered.</div>
            <div class="text-right">
                <button class="px-4 py-2 rounded-md bg-[#33595a] text-white" onclick="closeSuccess()">OK</button>
            </div>
        </div>
    </div>
    <div id="msg-error" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-xl p-6 border border-[#e1e7dd] max-w-md">
            <div class="text-[#3e4732] font-semibold mb-2">Send failed</div>
            <div id="msg-error-text" class="text-[#6b7280] mb-4">Something went wrong.</div>
            <div class="text-right">
                <button class="px-4 py-2 rounded-md bg-[#33595a] text-white" onclick="closeError()">OK</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const list = document.getElementById('conv-list');
    const header = document.getElementById('conv-header');
    const body = document.getElementById('conv-body');
    const replyText = document.getElementById('reply-text');
    const replySend = document.getElementById('reply-send');
    const successModal = document.getElementById('msg-success');
    const errorModal = document.getElementById('msg-error');
    const errorText = document.getElementById('msg-error-text');
    let currentConvId = null;

    function openSuccess(){ successModal.classList.remove('hidden'); successModal.classList.add('flex'); }
    function closeSuccess(){ successModal.classList.add('hidden'); successModal.classList.remove('flex'); }
    function openError(text){ errorText.textContent = text || 'Something went wrong.'; errorModal.classList.remove('hidden'); errorModal.classList.add('flex'); }
    function closeError(){ errorModal.classList.add('hidden'); errorModal.classList.remove('flex'); }
    window.closeSuccess = closeSuccess; window.closeError = closeError;

    list.addEventListener('click', function(e){
        const btn = e.target.closest('button[data-conv-id]');
        if (!btn) return;
        const convId = btn.getAttribute('data-conv-id');
        loadConversation(convId);
    });

    function loadConversation(id){
        currentConvId = id;
        body.innerHTML = '<div class="text-[#bcbabb]">Loading...</div>';
        replyText.disabled = true; replySend.disabled = true;
        fetch(`/provider/messages/${id}`, { headers: { 'Accept': 'application/json' }})
        .then(r => r.json())
        .then(data => {
            header.innerHTML = `
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-sm text-[#bcbabb]">Participant</div>
                        <div class="font-semibold text-[#3e4732]">${data.conversation.participant_name ?? data.conversation.participant_code ?? 'Unknown'}</div>
                        ${data.conversation.from_code ? `<div class="text-xs text-[#bcbabb]">From: ${data.conversation.from_code}</div>` : ''}
                    </div>
                    <div class="text-xs text-[#bcbabb]">Last: ${data.conversation.last_message_at ? new Date(data.conversation.last_message_at).toLocaleString() : ''}</div>
                </div>`;
            renderMessages(data.messages || []);
            replyText.disabled = false; replySend.disabled = false; replyText.focus();
        })
        .catch(() => {
            body.innerHTML = '<div class="text-red-600">Failed to load conversation.</div>';
        });
    }

    function renderMessages(messages){
        body.innerHTML = '';
        messages.forEach(m => {
            const wrap = document.createElement('div');
            wrap.className = 'mb-3 ' + (m.is_sender ? 'text-right' : 'text-left');
            const meta = `<div class=\"text-xs text-[#bcbabb] mt-1\">${m.created_human || ''} · ${m.read_at ? 'Read' : 'Unread'}</div>`;
            wrap.innerHTML = `<div class=\"inline-block px-3 py-2 rounded-lg ${m.is_sender ? 'bg-[#33595a] text-white' : 'bg-[#f8f1e1] text-[#3e4732]'}\">`+
                             `<div class=\"text-sm\">${escapeHtml(m.content)}</div>`+
                             `</div>`+meta;
            body.appendChild(wrap);
        });
        body.scrollTop = body.scrollHeight;
    }

    function escapeHtml(s){
        return s.replace(/[&<>"]/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[c]));
    }

    replySend.addEventListener('click', function(){
        const txt = replyText.value.trim();
        if (!txt || !currentConvId) return;
        replySend.disabled = true;
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        fetch(`/provider/messages/${currentConvId}/reply`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token, 'Accept': 'application/json' },
            body: JSON.stringify({ content: txt })
        })
        .then(async res => { if (!res.ok){ const d = await res.json().catch(()=>({message:'Failed'})); throw new Error(d.message||'Failed'); } return res.json(); })
        .then(d => {
            replyText.value = '';
            // append the new message preserving meta
            const existing = Array.from(body.children).map(div => ({
                is_sender: div.classList.contains('text-right'),
                content: div.querySelector('.text-sm')?.textContent || '',
                created_human: (div.querySelector('.text-xs')?.textContent || '').split('·')[0].trim(),
                read_at: null,
            }));
            renderMessages([...existing, d.data]);
            openSuccess();
        })
        .catch(err => openError(err.message || 'Failed to send'))
        .finally(() => { replySend.disabled = false; });
    });
});
</script>
@endpush
@endsection


