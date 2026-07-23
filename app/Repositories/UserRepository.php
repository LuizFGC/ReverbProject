<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Support\Collection;

class UserRepository
{
    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    public function getUserByRole(array $roles): Collection
    {
        return User::With(['cargo', 'departamento'])
            ->whereIn('role', $roles)
            ->get()
            ->map(fn ($user) => [
                'id' => $user->sid,
                'email' => $user->email,
                'nome' => $user->name,
                'cargo' => $user->cargo,
                'departamento' => $user->departamento,
            ]);

    }

    public function countUsers()
    {
        $agentes = User::WhereIn('role', ['agent','gerente'])->count();

        $clientes = User::Where('role', 'customer')->count();

        return [
            'agentes' => $agentes,
            'clientes' => $clientes,
        ];
    }

    public function create(array $user)
    {

        return User::create($user);
    }
}
