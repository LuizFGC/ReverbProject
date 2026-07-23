<?php

namespace App\Livewire\Forms;

use App\Http\Requests\Tickets\CreateUserRequest;
use Livewire\Form;

class CreateUserForm extends Form
{
    public string $nome = '';

    public string $email = '';

    public ?int $cargo = null;


    public array $cargos = [];


    public function rules(): array
    {
        return (new CreateUserRequest)->rules();
    }
    public function messages(): array
    {
        return [
            '*.required' => 'Campo obrigatório.',
        ];
    }

    public function resetFormulario(): void
    {
        $this->reset(['nome', 'email',]);
        $this->cargo = $this->cargos[0]['id'] ?? null;

        $this->resetErrorBag();
    }
}
