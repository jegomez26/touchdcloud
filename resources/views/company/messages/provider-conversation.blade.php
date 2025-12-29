@extends('company.provider-db')

@section('main-content')
<x-unified-messaging 
    :conversations="$conversations"
    :initialConversationId="$conversation->id"
    userType="provider"
    routePrefix="provider"
    emptyMessage="No conversations yet. Start by connecting with participants."
/>
@endsection


