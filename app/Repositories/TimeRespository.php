<?php

namespace App\Repositories;

use App\Models\PresenceLog;
use App\Models\User;

class TimeRespository
{
    public function getAll()
    {
        return User::where('role', 'agent')->get();
    }


}
