<?php

namespace App\Services;

use App\Repositories\TicketRepository;
use Illuminate\Pagination\LengthAwarePaginator;

class TicketService
{
    private TicketRepository $repository;

    public function __construct(TicketRepository $repository)
    {
        $this->repository = $repository;
    }

    public function createTicket($data)
    {
        $ticket = [

            'titulo' => $data['titulo'],
            'prioridade' => $data['prioridade'],
            'status' => 'Aberto',
            'categoria' => $data['categoria'],
            'descricao' => $data['descricao'],
            'anexos' => $data['anexos'],
            'user_id' => auth()->user()->sid,
            'user_name' => auth()->user()->name,

        ];

        return $this->repository->create($ticket);

    }

    public function updateTicket(string $id, array $ticket)
    {
        $this->repository->update($ticket, $id);
    }

    public function deleteTicket(string $id): void
    {
        $this->repository->delete($id);
    }

    public function getTickets(array $filtros): LengthAwarePaginator
    {
        return $this->repository->getAll($filtros);
    }

    public function getTicketById(string $id)
    {
        return $this->repository->getById($id);
    }
}
