<?php

namespace App\Services;

use App\Repositories\UserRepository;
use Illuminate\Support\Collection;

class UserService
{
    private UserRepository $repository;

    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getUser(array $roles): Collection
    {
        return $this->repository->getUserByRole($roles);
    }

    public function countUsers()
    {
        return $this->repository->countUsers();
    }
}
