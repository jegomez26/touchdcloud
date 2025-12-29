@extends('supcoor.sc-db')

@section('main-content')
<x-unified-messaging 
    :conversations="$conversations"
    :initialConversationId="$initialConversationId ?? null"
    userType="coordinator"
    routePrefix="sc"
    emptyMessage="No conversations yet. Start by connecting with participants."
/>
@endsection
