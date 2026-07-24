<?php

namespace App\Livewire\Pages\Agentes\Ticket;


use App\Services\TicketService;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\Attributes\Url;
use Livewire\WithPagination;


class Index extends Component
{

    use WithPagination;

    #[Url]
    public string $busca = '';

    #[Url]
    public string $status = 'todos';

    #[Url]
    public string $prioridade = 'todos';

    #[Url]
    public string $categoria = 'todos';

    public int $perPage = 10;

    #[Computed]
    public function tickets()
    {
        $service = app(TicketService::class);

        $filters = [
            'busca' => $this->busca,
            'status' => $this->status,
            'prioridade' => $this->prioridade,
            'categoria' => $this->categoria,
            'perPage' => $this->perPage,
        ];

        if (auth()->user()->role !== 'gerente') {
            $filters['agente'] = auth()->user()->sid;
        }

        return $service->getTickets($filters);
    }

    public function openTicket(string $id): void
    {
        $this->redirect("tickets/$id");
    }
    public function render()
    {
        return view('livewire.pages.agentes.ticket.index');
    }
}
