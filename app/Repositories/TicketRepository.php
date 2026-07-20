<?php

namespace App\Repositories;

use App\Models\Message;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

class TicketRepository
{
    public function create(array $ticket)
    {
        return Ticket::create($ticket);
    }

    public function delete(string $id)
    {
        $model = Ticket::findOrFail($id);

        $model->delete();
    }

    public function getAll(array $filtros): LengthAwarePaginator
    {
        return Ticket::query()
            ->when(
                $filtros['busca'],
                function ($query) use ($filtros) {

                    $busca = $filtros['busca'];

                    $query->where(function ($q) use ($busca) {

                        $q->where('titulo', 'like', "%{$busca}%")
                            ->orWhere('descricao', 'like', "%{$busca}%")
                            ->orWhere('id', 'like', "%{$busca}%")
                            ->orWhere('user_name', 'like', "%{$busca}%");

                    });

                }
            )
            ->when(
                $filtros['status'] !== 'todos',
                fn ($query) => $query->where('status', $filtros['status'])
            )
            ->when(
                $filtros['prioridade'] !== 'todos',
                fn ($query) => $query->where('prioridade', $filtros['prioridade'])
            )
            ->when(
                $filtros['categoria'] !== 'todos',
                fn ($query) => $query->where('categoria', $filtros['categoria'])
            )
            ->when(
                ! empty($filtros['user_id']),
                fn ($query) => $query->where('user_id', $filtros['user_id'])
            )
            ->when(
                ! empty($filtros['agente']),
                fn ($query) => $query->where('assigned_to', $filtros['agente'])
            )
            ->latest()
            ->paginate($filtros['perPage'] ?? 10);

    }

    public function getById(string $id)
    {
        $ticket = Ticket::findOrFail($id);

        return [
            'ticket' => $ticket,
            'messages' => $ticket->messages()->with('user')->get(),
            'customer' => $ticket->customer,
        ];
    }

    public function getByAgent($id)
    {
        return Ticket::where('assigned_to', $id)
            ->whereIn('status', [
                'Aberto',
                'Em andamento',
            ])
            ->count();
    }

    public function updateField($ticket, $field, $value): void
    {
        $ticket->update([
            $field => $value,
        ]);
    }
}
