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

    public function update(array $ticket, string $id)
    {
        $model = Ticket::findOrFail($id);

        $model->update($ticket);

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
                !empty($filtros['user_id']),
                fn ($query) => $query->where('user_id', $filtros['user_id'])
            )
            ->latest()
            ->paginate($filtros['perPage'] ?? 10);

    }

    public function getById(string $id)
    {
        $ticket = Ticket::findOrFail($id);

        $messages = Message::where(
            'ticket_id',
            $ticket->_id
        )
            ->orderBy('created_at')
            ->get();

        $customer = User::where('sid', $ticket->user_id)->first();

        return [
            'ticket' => $ticket,
            'messages' => $messages,
            'customer' => $customer,
        ];
    }

    public function getByAgent($agentesId)
    {
        return Ticket::whereIn('assigned_to', $agentesId)
            ->whereIn('status', [
                'Aberto',
                'Em andamento',
            ])
            ->get()
            ->groupBy('assigned_to');
    }
}
