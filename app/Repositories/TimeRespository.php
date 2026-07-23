<?php

namespace App\Repositories;

use App\Models\PresenceLog;
use App\Models\User;

class TimeRespository
{
    public function getAll()
    {
        return User::with(['cargo', 'departamento'])
            ->whereIn('role', ['agent', 'gerente'])
            ->get();
    }


}
