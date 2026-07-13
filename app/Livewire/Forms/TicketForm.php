<?php

namespace App\Livewire\Forms;

use App\Http\Requests\Tickets\CreateTicketRequest;
use Livewire\Form;

class TicketForm extends Form
{
    public string $titulo = '';

    public string $categoria = 'TI';

    public string $prioridade = 'Media';

    public string $descricao = '';

    public array $anexos = [];

    public function rules(): array
    {
        return (new CreateTicketRequest)->rules();
    }

    public function resetForm(): void
    {
        $this->reset();

        $this->categoria = 'TI';
        $this->prioridade = 'Media';
    }
}
