<?php

namespace App\Livewire\Pages\Cliente;

use App\Services\TicketService;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\Attributes\Url;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;

#[Layout('layouts.client')]

class Portal extends Component
{

    use WithPagination;

    #[Url]
    public string $busca = '';

    #[Url]
    public string $status = 'todos';

    public int $perPage = 10;

    public string $user_id = '';

    #[Url]
    public string $prioridade = 'todos';

    #[Url]
    public string $categoria = 'todos';
    #[Computed]
    public function tickets()
    {
        $service = app(TicketService::class);

        return $service->getTickets([
            'busca' => $this->busca,
            'status' => $this->status,
            'user_id'=> auth()->user()->sid,
            'perPage' => $this->perPage,
            'prioridade' => $this->prioridade,
            'categoria' => $this->categoria
        ]);
    }
    #[Computed]
    public function ticketsAbertos()
    {
        return $this->tickets
            ->getCollection()
            ->where('status', 'Aberto')
            ->count();
    }

    #[Computed]
    public function ticketsFechados()
    {
        return $this->tickets
            ->getCollection()
            ->where('status', 'Fechado')
            ->count();
    }
    public function render()
    {
        return view('livewire.pages.cliente.portal');
    }
}
