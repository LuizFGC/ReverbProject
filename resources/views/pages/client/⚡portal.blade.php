<?php

use Livewire\Component;
use Livewire\Attributes\Url;
use Livewire\Attributes\Layout;

new #[Layout('layouts.client')] class extends Component
{
    #[Url]
    public string $view = 'list'; // list | new

    #[Url]
    public string $filter = 'all'; // all | active | resolved

    #[Url]
    public string $search = '';

    public array $stats = [
        'active' => 0,
        'resolved' => 1,
    ];

    // --- New ticket form fields ---
    public string $subject = '';
    public string $description = '';
    public string $category = 'general';
    public string $priority = 'low';

    protected array $categories = [
        'technical' => ['label' => 'Técnico', 'icon' => 'cog-6-tooth'],
        'billing' => ['label' => 'Financeiro', 'icon' => 'document-text'],
        'hr' => ['label' => 'RH', 'icon' => 'user'],
        'general' => ['label' => 'Geral', 'icon' => 'question-mark-circle'],
    ];

    protected array $priorities = [
        'low' => 'Baixa',
        'medium' => 'Média',
        'high' => 'Alta',
        'urgent' => 'Urgente',
    ];

    public array $tickets = [
        [
            'id' => 'TS-2055',
            'title' => 'Erro de autenticação no login via SSO SAML',
            'description' => 'Após habilitar o SSO ontem no final do dia, vários colaboradores estão recebendo a mensagem "SAML Response invalid or expired" ao tentar fazer login pela nossa...',
            'priority' => 'high',
            'status' => 'resolved',
            'updatedAt' => 'há 09:47',
            'messageCount' => 6,
        ],
    ];

    public function getCategoriesProperty(): array
    {
        return $this->categories;
    }

    public function getPrioritiesProperty(): array
    {
        return $this->priorities;
    }

    public function getFilteredTicketsProperty()
    {
        return collect($this->tickets)
            ->when($this->search, fn ($items) => $items->filter(
                fn ($t) => str_contains(strtolower($t['title']), strtolower($this->search))
                    || str_contains(strtolower($t['id']), strtolower($this->search))
            ))
            ->when($this->filter === 'active', fn ($items) => $items->whereNotIn('status', ['resolved']))
            ->when($this->filter === 'resolved', fn ($items) => $items->where('status', 'resolved'))
            ->values();
    }

    public function goToNewTicket(): void
    {
        $this->view = 'new';
    }

    public function goToList(): void
    {
        $this->view = 'list';
    }

    public function createTicket(): void
    {
        $this->validate([
            'subject' => 'required|string|max:255',
            'description' => 'required|string|max:5000',
            'category' => 'required|string',
            'priority' => 'required|in:low,medium,high,urgent',
        ]);

        // TODO: persistir
        // Ticket::create([...]);

        $this->reset(['subject', 'description', 'category', 'priority']);
        $this->view = 'list';
        session()->flash('success', 'Chamado registrado com sucesso!');
    }
};
?>
<div>
    {{-- Header --}}
    <div class="flex items-start justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-white">Olá, {{ explode(' ', auth()->user()->name ?? 'Danilo Mendes')[0] }}! 👋</h1>
            <p class="text-zinc-400 mt-1">Bem-vindo à sua Central de Atendimento. Acompanhe ou inicie novos suportes abaixo.</p>
        </div>
        <div class="flex items-center gap-3">
            <div class="bg-zinc-900 border border-zinc-800 rounded-xl px-4 py-2 text-center">
                <p class="text-xs font-medium tracking-wide text-zinc-500">ATIVOS / ABERTOS</p>
                <p class="text-lg font-bold text-indigo-400">{{ $stats['active'] }}</p>
            </div>
            <div class="bg-zinc-900 border border-zinc-800 rounded-xl px-4 py-2 text-center">
                <p class="text-xs font-medium tracking-wide text-zinc-500">RESOLVIDOS</p>
                <p class="text-lg font-bold text-emerald-400">{{ $stats['resolved'] }}</p>
            </div>
        </div>
    </div>

    @if ($view === 'new')
        {{-- New ticket form --}}
        <div class="bg-zinc-900 border border-zinc-800 rounded-xl p-8">
            <h2 class="text-white font-semibold text-lg">Criar Nova Solicitação</h2>
            <p class="text-sm text-zinc-500 mt-1 mb-6">Preencha os detalhes técnicos para que nossa equipe especializada possa te ajudar.</p>

            <form wire:submit="createTicket" class="space-y-6">
                <flux:input
                    label="Assunto / Resumo do Problema"
                    placeholder="Ex: Não consigo finalizar pagamentos na rota de checkout ou erro 500"
                    wire:model="subject"
                />

                <flux:textarea
                    label="Descrição Detalhada do Ocorrido"
                    rows="4"
                    placeholder="Por favor, forneça o máximo de detalhes possível, como prints, passos para reproduzir, mensagens de erro do console ou logs da sua aplicação."
                    wire:model="description"
                />

                <div>
                    <flux:label>Categoria Relacionada</flux:label>
                    <div class="grid grid-cols-4 gap-3 mt-1.5">
                        @foreach ($this->categories as $value => $cat)
                            <button
                                type="button"
                                wire:click="$set('category', '{{ $value }}')"
                                class="flex flex-col items-center gap-2 py-4 rounded-lg border transition-colors {{ $category === $value ? 'border-emerald-500 text-emerald-400 bg-emerald-500/5' : 'border-zinc-700 text-zinc-400 hover:border-zinc-600' }}"
                            >
                                <flux:icon name="{{ $cat['icon'] }}" class="w-5 h-5" />
                                <span class="text-sm font-medium">{{ $cat['label'] }}</span>
                            </button>
                        @endforeach
                    </div>
                </div>

                <div>
                    <flux:label>Grau de Urgência / Prioridade</flux:label>
                    <div class="grid grid-cols-4 gap-3 mt-1.5">
                        @foreach ($this->priorities as $value => $label)
                            <button
                                type="button"
                                wire:click="$set('priority', '{{ $value }}')"
                                class="py-2.5 rounded-lg border text-sm font-medium transition-colors {{ $priority === $value ? 'border-zinc-400 text-white bg-zinc-800' : 'border-zinc-700 text-zinc-400 hover:border-zinc-600' }}"
                            >
                                {{ $label }}
                            </button>
                        @endforeach
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-zinc-800">
                    <flux:button type="button" variant="ghost" wire:click="goToList">Cancelar</flux:button>
                    <flux:button type="submit" variant="primary">Registrar Chamado</flux:button>
                </div>
            </form>
        </div>
    @else
        {{-- Tickets list --}}
        <div class="flex items-center gap-4 mb-4">
            <div class="flex-1">
                <flux:input icon="magnifying-glass" placeholder="Buscar por ID ou assunto..." wire:model.live.debounce.400ms="search" />
            </div>
            <div class="flex items-center bg-zinc-900 border border-zinc-800 rounded-lg p-1">
                <button wire:click="$set('filter', 'all')" class="px-4 py-1.5 rounded-md text-sm font-medium transition-colors {{ $filter === 'all' ? 'bg-indigo-600 text-white' : 'text-zinc-400 hover:text-white' }}">Todos</button>
                <button wire:click="$set('filter', 'active')" class="px-4 py-1.5 rounded-md text-sm font-medium transition-colors {{ $filter === 'active' ? 'bg-indigo-600 text-white' : 'text-zinc-400 hover:text-white' }}">Ativos</button>
                <button wire:click="$set('filter', 'resolved')" class="px-4 py-1.5 rounded-md text-sm font-medium transition-colors {{ $filter === 'resolved' ? 'bg-indigo-600 text-white' : 'text-zinc-400 hover:text-white' }}">Resolvidos</button>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            @forelse ($this->filteredTickets as $ticket)
                <a href="/portal/tickets/{{ $ticket['id'] }}" wire:key="{{ $ticket['id'] }}" class="bg-zinc-900 border border-zinc-800 rounded-xl p-5 hover:border-zinc-700 transition-colors">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-indigo-400 font-semibold text-sm">{{ $ticket['id'] }}</span>
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-medium text-amber-400 flex items-center gap-1">
                                <flux:icon name="x-circle" class="w-3.5 h-3.5" /> {{ $this->priorities[$ticket['priority']] }}
                            </span>
                            <span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-amber-500/15 text-amber-400 uppercase">
                                {{ $ticket['status'] === 'resolved' ? 'Resolvido' : 'Aberto' }}
                            </span>
                        </div>
                    </div>

                    <p class="text-white font-medium">{{ $ticket['title'] }}</p>
                    <p class="text-sm text-zinc-500 mt-1 line-clamp-2">{{ $ticket['description'] }}</p>

                    <div class="flex items-center justify-between mt-4 pt-4 border-t border-zinc-800 text-xs text-zinc-500">
                        <span class="flex items-center gap-1.5">
                            <flux:icon name="clock" class="w-3.5 h-3.5" /> Atualizado {{ $ticket['updatedAt'] }}
                        </span>
                        <span class="flex items-center gap-1.5 text-indigo-400">
                            <flux:icon name="chat-bubble-left" class="w-3.5 h-3.5" /> {{ $ticket['messageCount'] }} mensagens
                            <flux:icon name="chevron-right" class="w-3.5 h-3.5" />
                        </span>
                    </div>
                </a>
            @empty
                <div class="lg:col-span-2 text-center text-zinc-500 py-16">
                    Nenhum chamado encontrado.
                </div>
            @endforelse
        </div>
    @endif
</div>
