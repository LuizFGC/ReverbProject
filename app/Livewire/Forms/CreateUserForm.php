<?php

namespace App\Livewire\Forms;

use Livewire\Form;

class CreateUserForm extends Form
{

    public string $nome = '';
    public string $email = '';
    public string $cargo = 'Analista de Suporte Júnior';
    public string $departamento = 'Suporte Técnico';

    public array $cargos = [
        'Gerente de Suporte',
        'Analista de Suporte Sênior',
        'Analista de Suporte Júnior',
    ];

    public array $departamentos = [
        'Tecnologia',
        'Suporte Técnico',
        'Faturamento',
    ];

    public function resetFormulario(): void
    {
        $this->reset(['nome', 'email', 'editingId']);
        $this->cargo = $this->cargos[0];
        $this->departamento = $this->departamentos[0];
        $this->resetErrorBag();
    }
}
