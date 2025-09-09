@extends('company.provider-db')

@section('main-content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <a href="{{ route('provider.messages.index') }}" class="text-[#33595a] hover:underline">← Back to Inbox</a>
        <div class="text-sm text-[#bcbabb]">Last message: {{ $conversation->last_message_at?->diffForHumans() }}</div>
    </div>

    <div class="bg-white rounded-lg border border-[#e1e7dd] p-4 h-[60vh] overflow-y-auto">
        <div class="mb-4 text-sm text-[#bcbabb]">Participant: <span class="font-semibold text-[#3e4732]">{{ $conversation->participant->participant_code_name ?? 'Unknown' }}</span></div>

        @foreach($messages as $msg)
            <div class="mb-3 {{ $msg->sender_id === auth()->id() ? 'text-right' : 'text-left' }}">
                <div class="inline-block px-3 py-2 rounded-lg {{ $msg->sender_id === auth()->id() ? 'bg-[#33595a] text-white' : 'bg-[#f8f1e1] text-[#3e4732]' }}">
                    <div class="text-sm">{{ $msg->content }}</div>
                </div>
                <div class="text-xs text-[#bcbabb] mt-1">{{ $msg->created_at->format('M d, H:i') }}</div>
            </div>
        @endforeach
    </div>

    <form action="{{ route('provider.messages.reply', $conversation) }}" method="POST" class="bg-white rounded-lg border border-[#e1e7dd] p-4 flex items-center space-x-2">
        @csrf
        <textarea name="content" rows="2" class="flex-1 border border-[#e1e7dd] rounded-md p-2 focus:outline-none focus:ring-2 focus:ring-[#cc8e45]" placeholder="Type a message..." required></textarea>
        <button type="submit" class="px-4 py-2 rounded-md bg-[#33595a] text-white hover:bg-[#2C494A]">Send</button>
    </form>
</div>
@endsection


