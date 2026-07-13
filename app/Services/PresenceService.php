<?php

namespace App\Services;

use App\Repositories\PresenceRepository;

class PresenceService
{
    public function __construct(
        private PresenceRepository $presenceRepository
    ) {}

    public function update(string $userId, string $status): void
    {
        $this->presenceRepository->updatePresence($userId, $status);
    }

    public function getCurrentPresence(string $sid): string
    {
        return $this->presenceRepository->getCurrentPresence($sid);
    }
}
