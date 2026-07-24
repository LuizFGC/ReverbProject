<?php

namespace App\Services;

use App\Models\Ticket;
use App\Models\User;
use App\Notifications\TicketCriadoNotification;
use App\Repositories\TicketRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Notification;

class TicketService
{
    private TicketRepository $repository;

    public function __construct(TicketRepository $repository)
    {
        $this->repository = $repository;
    }

    public function createTicket($data)
    {
        $data = [

            'titulo' => $data['titulo'],
            'prioridade' => $data['prioridade'],
            'status' => 'Aberto',
            'categoria' => $data['categoria'],
            'descricao' => $data['descricao'],
            'anexos' => $data['anexos'],
            'user_id' => auth()->user()->sid,
            'user_name' => auth()->user()->name,

        ];

        $ticket = $this->repository->create($data);

        $this->sendNotification($ticket['user_name'], $ticket['id']);

        return $ticket;
    }

    // TODO : implementar Exclusao de ticket
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

    public function getTicketByAgente(string $id)
    {
        return $this->repository->getByAgent($id);
    }

    public function updateField(Ticket $ticket, string $field, mixed $value): void
    {
        $this->repository->updateField($ticket, $field, $value);
    }

    private function sendNotification(string $userName, $ticketId)
    {
        $agents = User::WhereIn('role', ['agent', 'gerente'])->get();

        Notification::send(
            $agents,
            new TicketCriadoNotification($userName, $ticketId)
        );
    }
}
