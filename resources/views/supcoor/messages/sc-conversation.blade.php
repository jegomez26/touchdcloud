@extends('supcoor.sc-db')

@section('main-content')
<x-unified-messaging 
    :conversations="$conversations"
    :initialConversationId="$conversation->id"
    userType="coordinator"
    routePrefix="sc"
    emptyMessage="No conversations yet. Start by connecting with participants."
/>
@endsection
