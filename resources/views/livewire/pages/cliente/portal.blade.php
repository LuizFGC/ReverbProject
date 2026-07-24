<div>
    {{-- Header --}}
    <div class="flex items-start justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-white">
                Olá, {{ explode(' ', auth()->user()->name ?? 'Danilo Mendes')[0] }}! 👋</h1>
            <p class="text-zinc-400 mt-1">Bem-vindo à sua Central de Atendimento. Acompanhe ou inicie novos suportes
                abaixo.</p>
        </div>
        <div class="flex items-center gap-3">
            <div class="bg-zinc-50 dark:bg-slate-900  border border-zinc-800 rounded-xl px-4 py-2 text-center">
                <p class="text-xs font-medium tracking-wide text-zinc-500">ATIVOS / ABERTOS</p>
                <p class="text-lg font-bold text-indigo-400">{{ $this->ticketsAbertos }}</p>
            </div>
            <div class="bg-zinc-50 dark:bg-slate-900 border border-zinc-800 rounded-xl px-4 py-2 text-center">
                <p class="text-xs font-medium tracking-wide text-zinc-500">RESOLVIDOS</p>
                <p class="text-lg font-bold text-emerald-400">{{ $this->ticketsFechados }}</p>
            </div>
        </div>
    </div>

    {{-- Tickets list --}}
    <div class="flex items-center gap-4 mb-4">
        <div class="flex-1">
            <flux:input icon="magnifying-glass" placeholder="Buscar por ID ou assunto..."
                        wire:model.live.debounce.400ms="busca"/>
        </div>
        <div class="flex items-center bg-zinc-50 dark:bg-slate-900  border border-zinc-800 rounded-lg p-1">
            <button wire:click="$set('status', 'todos')"
                    class="px-4 py-1.5 rounded-md text-sm font-medium transition-colors {{ $status === 'todos' ? 'bg-indigo-600 text-white' : 'text-zinc-400 hover:text-white' }}">
                Todos
            </button>
            <button wire:click="$set('status', 'Aberto')"
                    class="px-4 py-1.5 rounded-md text-sm font-medium transition-colors {{ $status === 'Aberto' ? 'bg-indigo-600 text-white' : 'text-zinc-400 hover:text-white' }}">
                Abertos
            </button>
            <button wire:click="$set('status', 'Em Atendimento')"
                    class="px-4 py-1.5 rounded-md text-sm font-medium transition-colors {{ $status === 'Em Atendimento' ? 'bg-indigo-600 text-white' : 'text-zinc-400 hover:text-white' }}">
                Em Atendimento
            </button>
            <button wire:click="$set('status', 'Fechado')"
                    class="px-4 py-1.5 rounded-md text-sm font-medium transition-colors {{ $status === 'Fechado' ? 'bg-indigo-600 text-white' : 'text-zinc-400 hover:text-white' }}">
                Resolvidos
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        @forelse ($this->tickets as $ticket)
            <a href="/ticket/{{ $ticket['id'] }}" wire:key="{{ $ticket['id'] }}"
               class="bg-zinc-50 dark:bg-slate-900  border border-zinc-800 rounded-xl p-5 hover:border-zinc-700 transition-colors">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-indigo-400 font-semibold text-sm"> Id: {{ $ticket['id'] }}</span>
                    <div class="flex items-center gap-2">
                            <span class="text-xs font-medium flex items-center gap-1">
                                <flux:icon name="x-circle" class="w-3.5 h-3.5"/> {{ $ticket['prioridade'] }}
                            </span>
                        <span
                            class="text-xs font-semibold px-2 py-0.5 rounded-full uppercase">
                                {{ $ticket['status']  }}
                            </span>
                    </div>
                </div>

                <p class="text-white font-medium">{{ $ticket['titulo'] }}</p>
                <p class="text-sm text-zinc-500 mt-1 line-clamp-2">{{ $ticket['descricao'] }}</p>

                <div class="flex items-center justify-between mt-4 pt-4 border-t border-zinc-800 text-xs text-zinc-500">
                        <span class="flex items-center gap-1.5">
                            <flux:icon name="clock" class="w-3.5 h-3.5"/> Atualizado {{ $ticket['updated_at'] }}
                        </span>
                    <span class="flex items-center gap-1.5 text-indigo-400">
                            <flux:icon name="chat-bubble-left" class="w-3.5 h-3.5"/> {{ $ticket['messageCount'] }} mensagens
                            <flux:icon name="chevron-right" class="w-3.5 h-3.5"/>
                        </span>
                </div>
            </a>
        @empty
            <div class="lg:col-span-2 text-center text-zinc-500 py-16">
                Nenhum chamado encontrado.
            </div>
        @endforelse
    </div>
</div>
