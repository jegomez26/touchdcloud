@extends('company.provider-db')

@section('main-content')
<x-unified-messaging 
    :conversations="$conversations"
    :initialConversationId="$initialConversationId ?? null"
    userType="provider"
    routePrefix="provider"
    emptyMessage="No conversations yet. Start by connecting with participants."
/>
@endsection


