<?php

namespace App\Services;

use App\Models\Cargo;
use App\Repositories\UserRepository;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

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


    public function createUser($data)
    {
        $cargo = Cargo::findOrFail($data['cargo']);

        $user = [
            'name' => $data['nome'],
            'email' => $data['email'],
            'role' => $data['tipo'],
            'password' => Hash::make('senha'),
            'cargo_id' => $cargo->id,
            'departamento_id' => $cargo->id_departamento,
            'sid' => Str::uuid(),
        ];

        return $this->repository->create($user);
    }

    private function sendNovoUsuarioEmail(array $user) {
    }
}
