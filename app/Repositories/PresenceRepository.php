<?php

namespace App\Repositories;

use App\Models\PresenceLog;

class PresenceRepository
{
    public function getPresenceLogs($agentesId)
    {
        return  PresenceLog::whereIn('user_id', $agentesId)
            ->latest()
            ->get()
            ->groupBy('user_id');
    }

    public function getCurrentPresence($sid)
    {
        return PresenceLog::where('user_id', $sid)
            ->latest()
            ->value('status') ?? 'offline';
    }

    public function updatePresence($userId, $status): void
    {
        PresenceLog::updateOrCreate(
            [
                'user_id' => $userId
            ],
            [
                'status' => $status
            ]
        );
    }
}
