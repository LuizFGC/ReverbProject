<?php

use App\Services\TimeService;
use App\Services\UserService;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Reactive;
use Livewire\Component;

new class extends Component {

    #[Reactive]
    public string $activeTab = 'agentes';

    #[Reactive]
    public string $search = '';

    #[Computed]
    public function agentes()
    {
        $role = ['agent', 'gerente'];

        $agentes = collect(app(UserService::class)->getUser($role));

        if (blank($this->search)) {
            return $agentes;
        }

        return $agentes->filter(function ($agente) {
            return $this->bate($agente, [
                'nome',
                'email',
                'cargo',
                'departamento',
            ]);
        })->values();
    }

    #[Computed]
    public function clientes()
    {
        $role = ['customer'];

        $clientes = collect(app(UserService::class)->getUser($role));

        if (blank($this->search)) {
            return $clientes;
        }

        return $clientes->filter(function ($cliente) {
            return $this->bate($cliente, [
                'nome',
                'email',
                'departamento',
            ]);
        })->values();
    }


    public function excluir(string $id): void
    {
        // TODO: trocar pela exclusão real, ex: User::findOrFail($id)->delete();
        $this->dispatch('usuario-salvo', mensagem: 'Usuário removido com sucesso.');
    }

    #[On('usuario-salvo')]
    public function atualizar(): void
    {
        unset($this->agentes, $this->clientes);
    }

    private function bate(array $item, array $campos): bool
    {
        if (blank($this->search)) {
            return true;
        }

        $termo = mb_strtolower(trim($this->search));

        foreach ($campos as $campo) {
            if (
                filled($item[$campo] ?? null) &&
                str_contains(mb_strtolower($item[$campo]), $termo)
            ) {
                return true;
            }
        }

        return false;
    }
};
?>

<div class="bg-zinc-50 dark:bg-slate-900 border border-zinc-800 rounded-xl overflow-hidden">
    <div class="flex items-center justify-between px-6 py-4 border-b border-zinc-800">
        <h2 class="text-xs font-semibold tracking-wide text-indigo-400 uppercase">
            Listagem de Equipe ({{ $activeTab === 'agentes' ? 'Agentes' : 'Clientes' }})
        </h2>
        <span class="text-xs text-zinc-500">
            Total: {{ $activeTab === 'agentes' ? $this->agentes->count() : $this->clientes->count() }}
        </span>
    </div>

    @if ($activeTab === 'agentes')
        <flux:table class="px-3">
            <flux:table.columns>
                <flux:table.column>Operador</flux:table.column>
                <flux:table.column>Cargo / Nível</flux:table.column>
                <flux:table.column>Departamento</flux:table.column>
                <flux:table.column>Ações</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse ($this->agentes as $agente)
                    <flux:table.row wire:key="agente-{{ $agente['id'] }}">
                        <flux:table.cell>
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-9 h-9 rounded-full bg-indigo-600 flex items-center justify-center text-xs font-semibold text-white shrink-0">
                                    {{ collect(explode(' ', $agente['nome']))->map(fn ($p) => mb_substr($p, 0, 1))->take(2)->implode('') }}
                                </div>
                                <div>
                                    <p class="text-white font-medium leading-tight">{{ $agente['nome'] }}</p>
                                    <p class="text-xs text-zinc-500">{{ $agente['email'] }}</p>
                                </div>
                            </div>
                        </flux:table.cell>

                        <flux:table.cell>
                            <flux:badge size="sm" >
                                {{$agente['cargo']['nome']}}
                            </flux:badge>
                        </flux:table.cell>

                        <flux:table.cell>
                            <flux:badge size="sm" >
                                {{$agente['departamento']['nome']}}
                            </flux:badge>
                        </flux:table.cell>


                        <flux:table.cell>
                            <div class="flex items-center gap-3">
                                <button
                                    wire:click="excluir('{{ $agente['id'] }}')"
                                    wire:confirm="Tem certeza que deseja remover este agente?"
                                    class="text-red-500 hover:text-red-400"
                                >
                                    <flux:icon name="trash" class="w-4 h-4"/>
                                </button>
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="5" class="text-center text-zinc-500 py-8">
                            Nenhum agente encontrado.
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    @else
        <flux:table class="px-3">
            <flux:table.columns>
                <flux:table.column>Cliente</flux:table.column>
                <flux:table.column>Departamento</flux:table.column>
                <flux:table.column>Ações</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse ($this->clientes as $cliente)
                    <flux:table.row wire:key="cliente-{{ $cliente['id'] }}">
                        <flux:table.cell>
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-9 h-9 rounded-full bg-emerald-600 flex items-center justify-center text-xs font-semibold text-white shrink-0">
                                    {{ collect(explode(' ', $cliente['nome']))->map(fn ($p) => mb_substr($p, 0, 1))->take(2)->implode('') }}
                                </div>
                                <div>
                                    <p class="text-white font-medium leading-tight">{{ $cliente['nome'] }}</p>
                                    <p class="text-xs text-zinc-500">{{ $cliente['email'] }}</p>
                                </div>
                            </div>
                        </flux:table.cell>

                        <flux:table.cell>{{ $cliente['departamento']['nome'] }}</flux:table.cell>

                        <flux:table.cell>
                            <div class="flex items-center gap-3">
                                <button
                                    wire:click="excluir('{{ $cliente['id'] }}')"
                                    wire:confirm="Tem certeza que deseja remover este cliente?"
                                    class="text-red-500 hover:text-red-400"
                                >
                                    <flux:icon name="trash" class="w-4 h-4"/>
                                </button>
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="4" class="text-center text-zinc-500 py-8">
                            Nenhum cliente encontrado.
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    @endif
</div>
