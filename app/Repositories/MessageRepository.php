<?php

namespace App\Repositories;

use App\Models\Message;

class MessageRepository
{
    public function create(
        string $ticketId,
        string $userId,
        string $content): Message
    {
        return Message::create([

            'ticket_id' => $ticketId,

            'user_id' => $userId,

            'content' => $content,

            'type' => 'text',

            'anexos' => []
            ]);
    }
}
