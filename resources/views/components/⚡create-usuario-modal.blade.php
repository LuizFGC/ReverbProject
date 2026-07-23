<?php

use App\Livewire\Forms\CreateUserForm;
use App\Services\CargoService;
use App\Services\DepartamentoService;
use App\Services\UserService;
use Livewire\Attributes\On;
use Livewire\Component;

new class extends Component {

    public bool $show = false;
    public string $formTab = 'agent';


    public CreateUserForm $form;

    public function mount(
        CargoService $cargoService,
    ): void {
        $this->form->cargos = $cargoService->getAllCargos();
        $this->form->cargo = $this->form->cargos[0]['id'] ?? null;
    }


    #[On('abrir-formulario')]
    public function abrir(?string $tipo = null): void
    {
        $this->form->resetFormulario();
        $this->formTab = $tipo ?? 'agent';
        $this->show = true;
    }

    public function selecionarTipo(string $tipo): void
    {
        $this->formTab = $tipo;
    }

    public function fechar(): void
    {
        $this->show = false;
        $this->form->resetFormulario();
    }

    public function salvar(UserService $service): void
    {
        $data = $this->form->validate();
        $data['tipo'] = $this->formTab;

        $service->createUser($data);

        $this->form->resetFormulario();
        $this->show = false;

        $this->dispatch('usuario-salvo', mensagem: 'Usuário criado com sucesso!');
    }

};
?>

<div>
    @if ($show)
        <div class="bg-zinc-50 dark:bg-slate-900 border border-zinc-800 rounded-xl p-6" wire:key="formulario-usuario">
            <div class="flex items-start justify-between mb-6 flex-wrap gap-4">
                <div>
                    <h2 class="text-white font-semibold flex items-center gap-2">
                        <flux:icon name="user-plus" class="w-4 h-4 text-indigo-400"/>
                        Formulário de Cadastro
                    </h2>
                    <p class="text-sm text-zinc-500">Preencha as informações para registrar o usuário no sistema
                        instantaneamente.</p>
                </div>

                <div class="inline-flex bg-zinc-950 border border-zinc-800 rounded-lg p-1 gap-1">
                    <button
                        type="button"
                        wire:click="selecionarTipo('agent')"
                        class="px-3 py-1.5 rounded-md text-xs font-semibold transition
                            {{ $formTab === 'agent' ? 'bg-indigo-600 text-white' : 'text-zinc-400 hover:text-white' }}"
                    >
                        Novo Agente
                    </button>
                    <button
                        type="button"
                        wire:click="selecionarTipo('customer')"
                        class="px-3 py-1.5 rounded-md text-xs font-semibold transition
                            {{ $formTab === 'customer' ? 'bg-indigo-600 text-white' : 'text-zinc-400 hover:text-white' }}"
                    >
                        Novo Cliente
                    </button>
                </div>
            </div>

            <div class="border-t border-zinc-800 pt-6">
                <div class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <flux:input
                            wire:model="form.nome"
                            label="Nome Completo"
                            placeholder="Ex: Pedro Henrique"
                        />
                        <flux:input
                            wire:model="form.email"
                            type="email"
                            label="Endereço de E-mail"
                            icon="envelope"
                            placeholder="nome@empresa.com"
                        />
                    </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <flux:select wire:model="form.cargo" label="Atribuição / Cargo (Role)">
                                @foreach ($this->form->cargos as $valor)
                                    <flux:select.option value="{{ $valor['id'] }}">{{ $valor['nome'] }}</flux:select.option>
                                @endforeach
                            </flux:select>

                        </div>

                    <div class="flex justify-end gap-3 border-t border-zinc-800 pt-6">
                        <flux:button type="button" variant="ghost" wire:click="fechar">
                            Cancelar
                        </flux:button>
                        <flux:button type="button" variant="primary" wire:click="salvar">
                            Registrar Conta no Sistema
                        </flux:button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
