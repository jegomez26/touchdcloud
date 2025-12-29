@extends('supcoor.sc-db')

@section('main-content')
<x-unified-messaging 
    :conversations="$conversations"
    :initialConversationId="$initialConversationId ?? null"
    userType="coordinator"
    routePrefix="sc"
    emptyMessage="You have no messages yet. Start by connecting with participants from the Unassigned Participants list."
/>
@endsection