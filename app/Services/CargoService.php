<?php

namespace App\Services;

use App\Repositories\CargoRepository;

class CargoService
{
    private CargoRepository $repository;

    public function __construct(CargoRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getAllCargos()
    {
        return $this->repository->getAll();
    }
}
