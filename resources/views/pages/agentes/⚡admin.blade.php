<?php

use App\Services\UserService;
use Livewire\Attributes\On;
use Livewire\Component;

new class extends Component {

    public string $activeTab = 'agentes';
    public string $search = '';
    public int $totalAgentes = 0;
    public int $totalClientes = 0;

    public function mount(UserService $service)
    {
        $data = $service->countUsers();

        $this->totalAgentes = $data['agentes'];
        $this->totalClientes = $data['clientes'];
    }

    public function switchTab(string $tab): void
    {
        $this->activeTab = $tab;
        $this->search = '';
    }

    #[On('usuario-salvo')]
    public function exibirMensagem(string $mensagem): void
    {
        session()->flash('status', $mensagem);
    }
};
?>

<div class="p-8 space-y-6">

    {{-- Header --}}
    <div class="flex items-start justify-between flex-wrap gap-4">
        <div>
            <h1 class="text-2xl font-bold text-white flex items-center gap-2">
                Painel do Administrador
                <flux:icon name="cog-6-tooth" class="w-5 h-5 text-zinc-500"/>
            </h1>
            <p class="text-zinc-400 mt-1">
                Cadastre novos operadores de suporte e clientes da plataforma, gerencie cargos, departamentos e
                permissões de acesso.
            </p>
        </div>

        <div class="flex items-center gap-3">
            <div class="bg-zinc-900 border border-zinc-800 rounded-xl px-4 py-2 flex items-center gap-3">
                <flux:icon name="shield-check" class="w-5 h-5 text-indigo-400"/>
                <div>
                    <p class="text-[10px] uppercase tracking-wide text-zinc-500">Agentes Ativos</p>
                    <p class="text-white font-semibold leading-none">{{ $totalAgentes }}</p>
                </div>
            </div>
            <div class="bg-zinc-900 border border-zinc-800 rounded-xl px-4 py-2 flex items-center gap-3">
                <flux:icon name="users" class="w-5 h-5 text-emerald-400"/>
                <div>
                    <p class="text-[10px] uppercase tracking-wide text-zinc-500">Clientes Cadastrados</p>
                    <p class="text-white font-semibold leading-none">{{ $totalClientes }}</p>
                </div>
            </div>
        </div>
    </div>

    @if (session('status'))
        <div class="bg-emerald-950/40 border border-emerald-900/50 text-emerald-400 text-sm rounded-lg px-4 py-3">
            {{ session('status') }}
        </div>
    @endif

    {{-- Tabs + botão cadastrar --}}
    <div class="flex items-center justify-between flex-wrap gap-3">
        <div class="inline-flex bg-zinc-900 border border-zinc-800 rounded-xl p-1 gap-1">
            <button
                wire:click="switchTab('agentes')"
                class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium transition
                    {{ $activeTab === 'agentes' ? 'bg-indigo-600 text-white' : 'text-zinc-400 hover:text-white' }}"
            >
                <flux:icon name="shield-check" class="w-4 h-4"/>
                Agentes de Suporte ({{ $totalAgentes }})
            </button>
            <button
                wire:click="switchTab('clientes')"
                class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium transition
                    {{ $activeTab === 'clientes' ? 'bg-indigo-600 text-white' : 'text-zinc-400 hover:text-white' }}"
            >
                <flux:icon name="user-group" class="w-4 h-4"/>
                Clientes do Portal ({{ $totalClientes }})
            </button>
        </div>

        <flux:button
            variant="primary"
            icon="user-plus"
            x-on:click="$dispatch('abrir-formulario', { tipo: @js($activeTab === 'clientes' ? 'cliente' : 'agente') })"
        >
            Cadastrar Novo Usuário
        </flux:button>
    </div>

    {{-- Busca --}}
    <flux:input
        wire:model.live.debounce.300ms="search"
        icon="magnifying-glass"
        placeholder="{{ $activeTab === 'agentes' ? 'Buscar agentes por nome, e-mail, cargo ou departamento…' : 'Buscar clientes por nome, e-mail ou empresa…' }}"
    />

    {{-- Formulário de cadastro/edição (componente filho) --}}
    <livewire:create-usuario-modal/>

    {{-- Tabela de listagem (componente filho) --}}
    <livewire:edit-usuario-table
        :active-tab="$activeTab"
        :search="$search"
        wire:key="tabela-usuarios"
    />
</div>
