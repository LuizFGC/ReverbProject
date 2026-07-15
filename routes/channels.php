<?php

use App\Models\Ticket;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('ticket.{ticketId}', function ($user, string $ticketId) {

    $ticket = Ticket::find($ticketId);

    if (! $ticket) {
        return false;
    }

    return true;
});

