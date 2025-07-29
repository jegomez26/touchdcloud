<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\Conversation;
use App\Models\User; // Assuming 'User' is your main authenticated user model

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

// Default User channel (often included, keep if you use it elsewhere)
Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Authorization for private conversation channels
Broadcast::channel('conversation.{conversationId}', function (User $user, $conversationId) {
    // Attempt to find the conversation
    $conversation = Conversation::find($conversationId);

    // If conversation doesn't exist, deny access
    if (!$conversation) {
        return false;
    }

    // --- Authorization Logic Based on your Conversation Model and User Roles ---

    // Scenario 1: User is a Support Coordinator involved in the conversation
    // Check if the current authenticated user's ID matches the SupportCoordinator's user_id
    // linked to this conversation. This assumes a User belongsTo SupportCoordinator.
    if ($conversation->supportCoordinator && $user->id === $conversation->supportCoordinator->user_id) {
        return true;
    }

    // Scenario 2: User is a Participant involved in the conversation
    // Check if the current authenticated user's ID matches the Participant's user_id
    // linked to this conversation. This assumes a User belongsTo Participant.
    if ($conversation->participant && $user->id === $conversation->participant->user_id) {
        return true;
    }

    // Scenario 3: User is a Provider involved in the conversation
    // Check if the current authenticated user's ID matches the Provider's user_id
    // linked to this conversation. This assumes a User belongsTo Provider.
    if ($conversation->provider && $user->id === $conversation->provider->user_id) {
        return true;
    }

    // If none of the above conditions are met, the user is not authorized.
    return false;
});