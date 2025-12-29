@extends('indiv.indiv-db')

@section('main-content')
<x-unified-messaging 
    :conversations="$conversations"
    :initialConversationId="$initialConversationId ?? null"
    userType="participant"
    routePrefix="participant"
    emptyMessage="You have no messages yet. Start a new conversation!"
/>
@endsection