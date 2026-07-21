<?php

use App\Services\DashboardService;
use App\Services\TicketService;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component {

    #[Computed]
    public function dashboardData()
    {
        $filters = [];

        if (auth()->user()->role !== 'gerente') {
            $filters['agente'] = auth()->user()->sid;
        }

        return app(DashboardService::class)->getDashboardData($filters);
    }

    #[Computed]
    public function stats(): array
    {
        return [
            [
                'label' => 'EM ABERTO',
                'value' => $this->dashboardData['totalAberto'],
                'icon' => 'inbox',
                'bg-color' => 'bg-blue-900',
                'color' => 'text-blue-500',
            ],
            [
                'label' => 'EM ATENDIMENTO',
                'value' => $this->dashboardData['emAtendimento'],
                'icon' => 'clock',
                'bg-color' => 'bg-yellow-900',
                'color' => 'text-yellow-500',
            ],
            [
                'label' => 'RESOLVIDOS',
                'value' => $this->dashboardData['fechado'],
                'icon' => 'check-circle',
                'bg-color' => 'bg-green-900',
                'color' => 'text-green-500',
            ],
            [
                'label' => 'TEMPO MÉDIO DE RESPOSTA',
                'value' => '14m', 'icon' => 'user-plus',
                'bg-color' => 'bg-indigo-950',
                'color' => ' text-indigo-500'],

        ];
    }


    public function openTicket(string $id): void
    {
        $this->redirect("tickets/$id");
    }

    public array $recentActivity = [

    ];
};
?>

<div class="p-8 space-y-6">

    <div class="flex items-start justify-between">
        <div>
            <h1 class="text-2xl font-bold text-white">Painel Executivo - Semanal</h1>
            <p class="text-zinc-400 mt-1">Sua visão consolidada de performance e fluxo de chamados ativos.</p>
        </div>
        <flux:button variant="primary" icon:trailing="arrow-up-right" href="/tickets">
            Ir para Chamados
        </flux:button>
    </div>


    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach ($this->stats as $stat)
            <div class="bg-zinc-50 dark:bg-slate-900 border-zinc-800 rounded-xl p-5">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-medium tracking-wide text-zinc-500">{{ $stat['label'] }}</span>
                    <div class="w-8 h-8 rounded-full {{$stat['bg-color']}} flex items-center justify-center">
                        <flux:icon name="{{ $stat['icon'] }}" class="w-4 h-4  {{$stat['color']}}"/>
                    </div>
                </div>

                <div class="text-3xl font-bold text-white mt-3">{{ $stat['value'] }}</div>
            </div>
        @endforeach
    </div>


    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">


        <div class="lg:col-span-2 bg-zinc-50 dark:bg-slate-900 border-zinc-800 rounded-xl p-6">
            <div class="flex items-start justify-between mb-6">
                <div>
                    <h2 class="text-white font-semibold">Volume Semanal de Chamados</h2>
                    <p class="text-sm text-zinc-500">Abertura vs Resolução diária de chamados de suporte</p>
                </div>
                <div class="flex items-center gap-4 text-sm">
                    <span class="flex items-center gap-1.5 text-zinc-400">
                        <span class="w-2 h-2 rounded-full bg-blue-500"></span> Abertos
                    </span>
                    <span class="flex items-center gap-1.5 text-zinc-400">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Resolvidos
                    </span>
                </div>
            </div>

            <div class="flex items-end justify-between gap-4 h-56 border-b border-zinc-800 pb-0">
                @foreach ($this->dashboardData['volumeSemanal'] as $dia)
                    <div class="flex flex-col items-center gap-2 flex-1">
                        <div class="flex items-end gap-1.5 h-48 w-full justify-center">
                            <div class="w-3 rounded-t bg-blue-500" style="height: {{ $dia['abertos'] }}%"></div>
                            <div class="w-3 rounded-t bg-emerald-500" style="height: {{ $dia['resolvidos'] }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="flex items-center justify-between gap-4 mt-2">
                @foreach ($this->dashboardData['volumeSemanal'] as $dia)
                    <span class="flex-1 text-center text-xs text-zinc-500">{{ $dia['dia'] }}</span>
                @endforeach
            </div>
        </div>


        <div class="bg-zinc-50 dark:bg-slate-900 border border-zinc-800 rounded-xl p-6 flex flex-col">
            <div class="flex items-center gap-2 mb-3">
                <flux:icon name="bell-alert" class="w-4 h-4 text-red-400"/>
                <h2 class="text-white font-semibold">Alertas Urgentes</h2>
            </div>
            <p class="text-sm text-zinc-500 mb-4">
                Chamados categorizados como <span class="text-white">Urgentes</span> requerem resposta com prioridade.
            </p>
            <div class="bg-red-950/40 border border-red-900/50 rounded-lg p-4 flex-1 max-h-80 overflow-y-auto divide-y divide-red-900/30">
                @forelse($this->dashboardData['urgentes'] as $urgente)
                    <div wire:key="urgente-{{ $urgente->id }}" class="flex items-start gap-3 py-3 first:pt-0 last:pb-0">
                        <flux:icon name="exclamation-triangle" class="w-5 h-5 text-red-400 shrink-0 mt-0.5"/>
                        <div class="flex-1">
                            <div class="flex items-center justify-between">
                                <span wire:click="openTicket('{{ $urgente->id }}')" class="text-red-400 font-semibold text-sm cursor-pointer"> ID: {{ $urgente->id }}</span>
                                <span class="text-xs text-zinc-500">{{ $urgente->created_at->format('d/m, H:i') }}</span>
                            </div>
                            <p class="text-white text-sm mt-1">{{ $urgente->titulo }}</p>
                            <p class="text-zinc-500 text-xs mt-1">{{ $urgente->user_name }}</p>
                        </div>
                    </div>
                @empty
                    <span class="text-zinc-500 text-sm">Não há tickets urgentes</span>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Recent activity table --}}
    <div class="bg-zinc-50 dark:bg-slate-900 border border-zinc-800 rounded-xl p-6">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-white font-semibold">Atividades Recentes</h2>
                <p class="text-sm text-zinc-500">Últimos chamados com interações ou alteração de status.</p>
            </div>
            <a href="/tickets" class="text-sm text-blue-400 hover:text-blue-300 font-medium">Ver todos os chamados</a>
        </div>

        <flux:table>
            <flux:table.columns>
                <flux:table.column>ID</flux:table.column>
                <flux:table.column>Assunto / Título</flux:table.column>
                <flux:table.column>Status</flux:table.column>
                <flux:table.column>Prioridade</flux:table.column>
                <flux:table.column>Atribuído a</flux:table.column>
                <flux:table.column>Atualizado</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse ($recentActivity as $row)
                    <flux:table.row>
                        <flux:table.cell>{{ $row['id'] }}</flux:table.cell>
                        <flux:table.cell>{{ $row['title'] }}</flux:table.cell>
                        <flux:table.cell>{{ $row['status'] }}</flux:table.cell>
                        <flux:table.cell>{{ $row['priority'] }}</flux:table.cell>
                        <flux:table.cell>{{ $row['assignee'] }}</flux:table.cell>
                        <flux:table.cell>{{ $row['updated_at'] }}</flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="6" class="text-center text-zinc-500 py-6">
                            Nenhuma atividade recente
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    </div>
</div>
