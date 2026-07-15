<?php

use App\Services\PresenceService;
use App\Services\TimeService;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component {


    #[Computed(cache: false)]
    public function agentes()
    {
        return app(TimeService::class)->getAgentes();
    }

    public function setPresence(string $presence): void
    {
        app(PresenceService::class)->update(
            auth()->user()->sid,
            $presence
        );
        unset($this->agentes);
        $this->dispatch('$refresh');
    }

    protected function presenceMeta(string $presence): array
    {
        return match ($presence) {
            'online' => ['label' => 'Disponível', 'dot' => 'bg-emerald-500', 'badge' => 'bg-emerald-500/15 text-emerald-400'],
            'away' => ['label' => 'Ausente', 'dot' => 'bg-amber-500', 'badge' => 'bg-amber-500/15 text-amber-400'],
            default => ['label' => 'Offline', 'dot' => 'bg-zinc-500', 'badge' => 'bg-zinc-700/50 text-zinc-400'],
        };
    }
};
?>
<div class="p-8 space-y-6">

    {{-- Header --}}
    <div>
        <h1 class="text-2xl font-bold text-white">Time de Atendimento</h1>
        <p class="text-zinc-400 mt-1">Monitore a presença e distribuição de chamados ativos por agente.</p>
    </div>

    {{-- Agents grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach ($this->agentes as $agent)
            @php
                $user = $agent['user'];
                $meta = $this->presenceMeta($agent['presence']);
            @endphp

            <div @class([
                'bg-zinc-50 dark:bg-slate-900  border rounded-xl p-5',
                'border-indigo-500 ring-1 ring-indigo-500' => $agent['isCurrentUser'] ?? false,
                'border-zinc-800' => !($agent['isCurrentUser'] ?? false),
            ])>
                {{-- Top: avatar + name + status badge --}}
                <div class="flex items-start justify-between">
                    <div class="flex items-center gap-3">
                        <div class="relative">
                            <div
                                class="w-10 h-10 rounded-full bg-zinc-50 dark:bg-slate-900  text-indigo-300 font-semibold flex items-center justify-center text-sm">
                                {{ $user['name'] }}
                            </div>
                            <span
                                class="absolute bottom-0 right-0 w-2.5 h-2.5 rounded-full border-2 border-zinc-900 {{ $meta['dot'] }}"></span>
                        </div>
                        <div>
                            <div class="flex items-center gap-1.5">
                                <span class="text-white font-semibold">{{ $user['name'] }}</span>
                                @if ($agent['isCurrentUser'] ?? false)
                                    <span class="text-xs text-indigo-400">Você</span>
                                @endif
                            </div>
                            <p class="text-sm text-zinc-500">{{ $user['cargo']}}</p>
                        </div>
                    </div>

                    <span class="text-xs font-medium px-2.5 py-1 rounded-full {{ $meta['badge'] }}">
                                            {{ $meta['label'] }}
                                        </span>
                </div>

                <div class="border-t border-zinc-800 mt-4 pt-4 space-y-2">
                    <p class="text-sm text-zinc-400 flex items-center gap-2">
                        <flux:icon name="building-office-2" class="w-4 h-4 text-zinc-500"/>
                        Departamento: <span class="text-white font-medium">{{ $user['cargo'] }}</span>
                    </p>
                    <p class="text-sm text-zinc-400 flex items-center gap-2">
                        <flux:icon name="envelope" class="w-4 h-4 text-zinc-500"/>
                        {{ $user['email'] }}
                    </p>
                </div>

                <div class="border-t border-zinc-800 mt-4 pt-4">
                    <div class="flex items-center justify-between bg-zinc-800/50 rounded-lg px-3 py-2">
                        <span class="text-sm text-zinc-400 flex items-center gap-2">
                            <flux:icon name="signal" class="w-4 h-4 text-zinc-500"/>
                            Atendimentos Ativos
                        </span>
                        <span class="text-xs font-medium bg-zinc-700 text-zinc-300 px-2 py-0.5 rounded-full">
                                                    {{ $agent['activeTickets'] }} chamados
                                                </span>
                    </div>
                </div>

                {{-- Presence control only on current user's card --}}
                @if ($agent['isCurrentUser'] ?? false)
                    <div class="border-t border-zinc-800 mt-4 pt-4">
                        <p class="text-xs font-medium tracking-wide text-zinc-500 mb-2">ALTERAR SUA PRESENÇA</p>
                        <div class="grid grid-cols-3 gap-2">

                            <button
                                wire:click="setPresence('online')"
                                @class([
                                    'text-sm font-medium py-1.5 rounded-lg border transition-colors',
                                    'bg-emerald-500 border-emerald-500 text-white'
                                        => $agent['presence'] === 'online',
                                    'border-zinc-700 text-zinc-300 hover:bg-zinc-800'
                                        => $agent['presence'] !== 'online',
                                ])
                            >
                                Online
                            </button>

                            <button
                                wire:click="setPresence('away')"
                                @class([
                                    'text-sm font-medium py-1.5 rounded-lg border transition-colors',
                                    'bg-amber-500 border-amber-500 text-white' => $agent['presence'] === 'away',
                                    'border-zinc-700 text-zinc-300 hover:bg-zinc-800' =>$agent['presence'] !== 'away',
                                ])
                            >
                                Ausente
                            </button>
                            <button
                                wire:click="setPresence('offline')"
                                @class([
                                    'text-sm font-medium py-1.5 rounded-lg border transition-colors',
                                    'bg-zinc-600 border-zinc-600 text-white' => $agent['presence'] === 'offline',
                                    'border-zinc-700 text-zinc-300 hover:bg-zinc-800' => $agent['presence'] !== 'offline',
                                ])
                            >
                                Offline
                            </button>
                        </div>
                    </div>
                @endif
            </div>
        @endforeach
    </div>
</div>
