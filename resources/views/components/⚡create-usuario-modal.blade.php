<?php

use App\Livewire\Forms\CreateUserForm;
use Livewire\Attributes\On;
use Livewire\Component;

new class extends Component {

    public bool $show = false;
    public string $formTab = 'agente';

    public ?string $editingId = null;

    public CreateUserForm $form;

    public function mount(): void
    {
        $this->form = new CreateUserForm($this, 'form');
    }

    #[On('abrir-formulario')]
    public function abrir(?string $tipo = null): void
    {
        $this->form->resetFormulario();
        $this->formTab = $tipo ?? 'agente';
        $this->show = true;
    }

    #[On('editar-usuario')]
    public function carregarParaEdicao(string $id): void
    {
        // TODO: trocar por busca real, ex: User::findOrFail($id)

        $registro =

        $this->editingId = $registro['id'];
        $this->nome = $registro['nome'];
        $this->email = $registro['email'];
        $this->cargo = $registro['cargo'];
        $this->departamento = $registro['departamento'];
        $this->formTab = 'agente';
        $this->show = true;
    }

    public function selecionarTipo(string $tipo): void
    {
        $this->formTab = $tipo;
    }

    public function fechar(): void
    {
        $this->show = false;
        $this->resetFormulario();
    }

    public function salvar(): void
    {
        $regras = [
            'nome' => ['required', 'string', 'min:3', 'max:120'],
            'email' => ['required', 'email', 'max:150'],
        ];

        if ($this->formTab === 'agente') {
            $regras['cargo'] = ['required', 'string'];
            $regras['departamento'] = ['required', 'string'];
        }

        $this->validate($regras);

        // TODO: trocar pela persistência real, ex:
        // if ($this->editingId) {
        //     User::findOrFail($this->editingId)->update([...]);
        // } else {
        //     User::create([...]);
        // }

        $this->dispatch('usuario-salvo', mensagem: $this->editingId
            ? 'Usuário atualizado com sucesso.'
            : 'Usuário cadastrado com sucesso.');

        $this->fechar();
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
                        wire:click="selecionarTipo('agente')"
                        class="px-3 py-1.5 rounded-md text-xs font-semibold transition
                            {{ $formTab === 'agente' ? 'bg-indigo-600 text-white' : 'text-zinc-400 hover:text-white' }}"
                    >
                        Novo Agente
                    </button>
                    <button
                        type="button"
                        wire:click="selecionarTipo('cliente')"
                        class="px-3 py-1.5 rounded-md text-xs font-semibold transition
                            {{ $formTab === 'cliente' ? 'bg-indigo-600 text-white' : 'text-zinc-400 hover:text-white' }}"
                    >
                        Novo Cliente
                    </button>
                </div>
            </div>

            <div class="border-t border-zinc-800 pt-6">
                <form wire:submit="salvar" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <flux:input
                            wire:model="nome"
                            label="Nome Completo"
                            placeholder="Ex: Pedro Henrique"
                        />
                        <flux:input
                            wire:model="email"
                            type="email"
                            label="Endereço de E-mail"
                            icon="envelope"
                            placeholder="nome@empresa.com"
                        />
                    </div>

                    @if ($formTab === 'agente')
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <flux:select wire:model="cargo" label="Atribuição / Cargo (Role)">
                                @foreach ($this->form->cargos as $valor)
                                    <flux:select.option value="{{ $valor }}">{{ $valor }}</flux:select.option>
                                @endforeach
                            </flux:select>

                            <flux:select wire:model="departamento" label="Departamento Técnico">
                                @foreach ($this->form->departamentos as $dep)
                                    <flux:select.option value="{{ $dep }}">{{ $dep }}</flux:select.option>
                                @endforeach
                            </flux:select>
                        </div>
                    @endif

                    <div class="flex justify-end gap-3 border-t border-zinc-800 pt-6">
                        <flux:button type="button" variant="ghost" wire:click="fechar">
                            Cancelar
                        </flux:button>
                        <flux:button type="submit" variant="primary">
                            Registrar Conta no Sistema
                        </flux:button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
