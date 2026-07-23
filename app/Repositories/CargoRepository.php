<?php

namespace App\Repositories;

use App\Models\Cargo;

class CargoRepository
{
    public function getAll(): array
    {
        return Cargo::query()
            ->select(['id', 'nome'])
            ->get()
            ->toArray();
    }
}
