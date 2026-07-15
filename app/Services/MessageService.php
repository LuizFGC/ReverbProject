<?php

namespace App\Services;

use App\Events\SendMessageEvent;
use App\Models\Message;
use App\Repositories\MessageRepository;

class MessageService
{
    private MessageRepository $repository;

    public function __construct(MessageRepository $repository)
    {
        $this->repository = $repository;
    }

    public function createMessage(
        string $ticketId,
        string $userId,
        string $content
    ): Message {
        $message = $this->repository->create($ticketId, $userId, $content);


        event(new SendMessageEvent($message));

        return $message;
    }
}
