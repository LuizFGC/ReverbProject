<?php

namespace App\Livewire\Forms;

use App\Http\Requests\Tickets\CreateTicketRequest;
use Livewire\Form;

class CreateTicketForm extends Form
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
    public function messages(): array
    {
        return [
            '*.required' => 'Campo obrigatório.',
            '*.string' => 'O campo deve ser um texto.',
            '*.array' => 'O campo deve ser uma lista.',
            'prioridade.in' => 'Prioridade inválida.',
        ];
    }

    public function resetForm(): void
    {
        $this->reset();

        $this->categoria = 'TI';
        $this->prioridade = 'Media';
    }
}
