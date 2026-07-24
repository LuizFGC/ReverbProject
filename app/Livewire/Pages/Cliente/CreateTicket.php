<?php

namespace App\Livewire\Pages\Cliente;

use App\Livewire\Forms\CreateTicketForm;
use App\Services\TicketService;
use Flux\Flux;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithFileUploads;

#[Layout('layouts.client')]

class CreateTicket extends Component
{

    use WithFileUploads;


    public bool $showModal = false;

    public CreateTicketForm $form;

    public function mount(): void
    {
        $this->form = new CreateTicketForm($this, 'form');
    }

    public array $categories = [
        'RH' => [
            'label' => 'RH',
            'icon' => 'user'
        ],
        'TI' => [
            'label' => 'Tecnico',
            'icon' => 'computer-desktop'
        ],
        'financeiro' => [
            'label' => 'Financeiro',
            'icon' => 'currency-dollar'
        ],
        'Geral' => [
            'label' => 'Geral',
            'icon' => 'question-mark-circle'
        ]
    ];

    protected array $priorityOptions = [
        'Baixa' => [
            'label' => 'Baixa',
            'active' => 'border-zinc-500 text-zinc-300 bg-zinc-800',
            'inactive' => 'border-zinc-700 text-zinc-400'
        ],

        'Media' => [
            'label' => 'Média',
            'active' => 'border-indigo-500 text-indigo-400 bg-indigo-500/10',
            'inactive' => 'border-zinc-700 text-zinc-400'
        ],

        'Alta' => [
            'label' => 'Alta',
            'active' => 'border-amber-500 text-amber-400 bg-amber-500/10',
            'inactive' => 'border-zinc-700 text-zinc-400'
        ],

        'Urgente' => [
            'label' => 'Urgente',
            'active' => 'border-red-500 text-red-400 bg-red-500/10',
            'inactive' => 'border-zinc-700 text-zinc-400'
        ],
    ];

    public function removerAnexos(int $index): void
    {
        unset($this->form->anexos[$index]);

        $this->form->anexos = array_values(
            $this->form->anexos
        );
    }


    public function createTicket(TicketService $service): void
    {
        $data = $this->form->validate();


        $service->createTicket($data);

        $this->form->resetForm();

        $this->dispatch(
            'ticket-created',
            message: 'Chamado criado com sucesso!');

    }

    #[On('ticket-created')]
    public function showSuccess($message): void
    {
        Flux::toast(
            text: $message,
            variant: 'success',
            position: "top end"
        );
    }
    public function render()
    {
        return view('livewire.pages.cliente.create-ticket');
    }
}
