@extends('indiv.indiv-db')

@section('main-content')
<x-unified-messaging 
    :conversations="$conversations"
    :initialConversationId="$conversation->id"
    userType="participant"
    routePrefix="participant"
    emptyMessage="You have no messages yet. Start a new conversation!"
/>
@endsection
