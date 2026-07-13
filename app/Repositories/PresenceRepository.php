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
        $last = PresenceLog::where('user_id', $userId)
            ->latest()
            ->first();

        if ($last?->status === $status) {
            return;
        }

        PresenceLog::create([
            'user_id' => $userId,
            'status' => $status,
        ]);
    }
}
