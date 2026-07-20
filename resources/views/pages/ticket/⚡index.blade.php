<?php

use App\Services\TicketService;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\Attributes\Url;
use Livewire\WithPagination;

new class extends Component {

    use WithPagination;

    #[Url]
    public string $busca = '';

    #[Url]
    public string $status = 'todos';

    #[Url]
    public string $prioridade = 'todos';

    #[Url]
    public string $categoria = 'todos';

    public int $perPage = 10;

    #[Computed]
    public function tickets()
    {
        $service = app(TicketService::class);

        $filters = [
            'busca' => $this->busca,
            'status' => $this->status,
            'prioridade' => $this->prioridade,
            'categoria' => $this->categoria,
            'perPage' => $this->perPage,
        ];

        if (auth()->user()->role !== 'gerente') {
            $filters['agente'] = auth()->user()->sid;
        }

        return $service->getTickets($filters);
    }

    public function openTicket(string $id): void
    {
        $this->redirect("tickets/$id")    ;
    }

};
?>
<div class="p-8 space-y-6">


    <div class="flex items-start justify-between">
        <div>
            <h1 class="text-2xl font-bold text-white">Fila de Atendimento</h1>
            <p class="text-zinc-400 mt-1">Gerencie, filtre e acompanhe todas as solicitações recebidas.</p>
        </div>
        <flux:modal.trigger name="create-ticket-modal">
            <flux:button variant="primary" icon="plus" href="#new-ticket">
                Novo Chamado
            </flux:button>
        </flux:modal.trigger>
    </div>

    <flux:modal name="create-ticket-modal" class="bg-zinc-50 dark:bg-slate-900 ">
        <livewire:create-ticket-modal/>
    </flux:modal>

    {{-- Filtros --}}
    <div class="bg-zinc-50 dark:bg-slate-900  rounded-xl p-5 flex flex-wrap items-end gap-4">
        <div class="min-w-[160px]">
            <flux:select label="Status" wire:model.live="status">
                <flux:select.option value="todos">Todos os Status</flux:select.option>
                <flux:select.option value="Aberto">Aberto</flux:select.option>
                <flux:select.option value="Pendente">Pendente</flux:select.option>
                <flux:select.option value="Fechado">Fechado</flux:select.option>
            </flux:select>
        </div>

        <div class="min-w-[180px]">
            <flux:select label="Prioridade" wire:model.live="prioridade">
                <flux:select.option value="todos">Todas as Prioridades</flux:select.option>
                <flux:select.option value="Urgente">Urgente</flux:select.option>
                <flux:select.option value="Alta">Alta</flux:select.option>
                <flux:select.option value="Media">Média</flux:select.option>
                <flux:select.option value="Baixa">Baixa</flux:select.option>
            </flux:select>
        </div>

        <div class="min-w-[180px]">
            <flux:select label="Categoria" wire:model.live="categoria">
                <flux:select.option value="todos">Todas as Categorias</flux:select.option>
                <flux:select.option value="TI">Técnico</flux:select.option>
                <flux:select.option value="financeiro">Financeiro</flux:select.option>
                <flux:select.option value="Geral">Geral</flux:select.option>
            </flux:select>
        </div>

        <div class="flex-1 min-w-[240px] ml-auto">
            <flux:input icon="magnifying-glass" placeholder="Buscar nesta lista..."
                        wire:model.live.debounce.400ms="busca"/>
        </div>
    </div>

    {{--  lista --}}
         <flux:table :paginate="$this->tickets">
            <flux:table.columns>
                <flux:table.columns>
                    <flux:table.column>Usuario</flux:table.column>
                    <flux:table.column>Chamado</flux:table.column>
                    <flux:table.column>Status</flux:table.column>
                    <flux:table.column>Prioridade</flux:table.column>
                </flux:table.columns>
                <flux:table.rows>
                    @forelse($this->tickets as $ticket)
                        <flux:table.row :key="$ticket->id">
                            <flux:table.cell>
                                {{ $ticket->user_name }}
                            </flux:table.cell>

                            <flux:table.cell>
                                {{ $ticket->titulo }}
                            </flux:table.cell>

                            <flux:table.cell class="py-0">
                                <flux:badge size="sm">
                                    {{ $ticket->status }}
                                </flux:badge>
                            </flux:table.cell>

                            <flux:table.cell>
                                <flux:badge size="sm">
                                    {{ $ticket->prioridade }}
                                </flux:badge>
                            </flux:table.cell>

                            <flux:table.cell class="py-0">
                                <flux:button
                                    class="cursor-pointer"
                                    variant="ghost"
                                    size="sm"
                                    icon="eye"
                                    wire:click="openTicket('{{ $ticket->id }}')"
                                />
                            </flux:table.cell>
                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="5">
                                <div class="text-center py-4 text-zinc-500">
                                    Nenhum ticket encontrado.
                                </div>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
         </flux:table>
</div>
