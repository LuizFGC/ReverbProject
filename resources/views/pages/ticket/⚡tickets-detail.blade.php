<?php

use App\Models\Message;
use App\Models\Ticket;
use App\Models\User;
use App\Services\TicketService;
use Illuminate\Support\Collection;
use Livewire\Component;

new class extends Component {

    public string $ticketId;

    public ?Ticket $ticket = null;

    public User $customer;

    public Collection $messages;

    public function mount(string $id): void
    {

        $this->ticketId = $id;

        $service = app(TicketService::class);

        $data = $service->getTicketById($id);

        $this->ticket = $data['ticket'];
        $this->messages = $data['messages'];
        $this->customer = $data['customer'];
    }

    public string $reply = '';


    public function sendReply(): void
    {
        $this->validate(['reply' => 'required|string|max:5000']);

        $this->messages[] = [
            'from' => 'agent',
            'author' => auth()->user()->name ?? 'Você',
            'initials' => 'LF',
            'time' => now()->format('H:i'),
            'text' => $this->reply,
        ];

        // TODO: persistir a mensagem
        // $ticket->messages()->create([...]);

        $this->reply = '';
    }

    public function updateStatus(string $status): void
    {
        $this->ticket['status'] = $status;
        // TODO: persistir
    }

    public function updatePrioridade(string $priority): void
    {
        $this->ticket['priority'] = $priority;
        // TODO: persistir
    }
};
?>
<div class="flex h-[calc(100vh-4rem)]">

    {{-- Left panel: ticket + customer details --}}
    <div class="w-80 shrink-0 border-r border-zinc-800 overflow-y-auto p-6">
        <button wire:click="redirect('/tickets')"
                class="flex items-center gap-2 text-zinc-400 hover:text-white text-sm mb-6">
            <flux:icon name="arrow-left" class="w-4 h-4"/>
        </button>

        <p class="text-xs font-medium tracking-wide text-zinc-500">ID DO CHAMADO</p>
        <p class="text-xl font-bold text-white mt-1">{{ $ticket->id }}</p>

        <div class="border-t border-zinc-800 mt-5 pt-5 space-y-5">
            <div>
                <flux:label class="text-xs tracking-wide">Alterar Status</flux:label>
                <flux:select wire:model.live="ticket.status" class="mt-1.5">
                    <flux:select.option value="Acerto">Aberto (Aguardando)</flux:select.option>
                    <flux:select.option value="Em Atendimento">Em Atendimento</flux:select.option>
                    <flux:select.option value="Resolvido">Resolvido</flux:select.option>
                    <flux:select.option value="Fechado">Fechado</flux:select.option>
                </flux:select>
            </div>

            <div>
                <flux:label class="text-xs tracking-wide">Alterar Prioridade</flux:label>
                <flux:select wire:model.live="ticket.priority" class="mt-1.5">
                    <flux:select.option value="Baixa">Prioridade Baixa</flux:select.option>
                    <flux:select.option value="Media">Prioridade Média</flux:select.option>
                    <flux:select.option value="Alta">Prioridade Alta</flux:select.option>
                    <flux:select.option value="Urgente">Prioridade Urgente 🔺</flux:select.option>
                </flux:select>
            </div>

            <div>
                <flux:label class="text-xs tracking-wide">Categoria</flux:label>
                <div class="mt-1.5 bg-zinc-800/60 border border-zinc-700 rounded-lg px-3 py-2 text-sm text-zinc-300">
                    {{ $ticket->categoria }}
                </div>
            </div>

            <div>
                <flux:label class="text-xs tracking-wide">Agente Responsável</flux:label>
                <flux:select wire:model.live="ticket.assignee" class="mt-1.5">
                    <flux:select.option value="Mariana Costa (Faturamento)">Mariana Costa (Faturamento)
                    </flux:select.option>
                    <flux:select.option value="Ana Souza (Suporte Técnico)">Ana Souza (Suporte Técnico)
                    </flux:select.option>
                    <flux:select.option value="Carlos Silva (Suporte Técnico)">Carlos Silva (Suporte Técnico)
                    </flux:select.option>
                </flux:select>
            </div>
        </div>

        <div class="border-t border-zinc-800 mt-5 pt-5">
            <p class="text-xs font-medium tracking-wide text-zinc-500 mb-3">DADOS DO CLIENTE</p>

            <div class="flex items-center gap-3 mb-4">
                <div
                    class="w-10 h-10 rounded-full bg-zinc-700 text-zinc-200 font-semibold flex items-center justify-center text-sm">
                    {{ $customer['name'] }}
                </div>
                <div>
                    <p class="text-white font-medium">{{ $customer['name'] }}</p>
                    <p class="text-xs text-indigo-400 font-medium">{{ strtoupper($customer['cargo']) }}</p>
                </div>
            </div>

            <div class="space-y-2 text-sm text-zinc-400">
                <p class="flex items-center gap-2">
                    <flux:icon name="envelope" class="w-4 h-4 text-zinc-500"/> {{ $customer['email'] }}
                </p>
                <p class="flex items-center gap-2">
                    <flux:icon name="phone" class="w-4 h-4 text-zinc-500"/> {{ $customer['email'] }}
                </p>
                <p class="flex items-center gap-2">
                    <flux:icon name="building-office-2" class="w-4 h-4 text-zinc-500"/> {{ $customer['cargo'] }}
                </p>
            </div>
        </div>

        <div class="border-t border-zinc-800 mt-5 pt-5 text-xs text-zinc-500 space-y-1">
            <p>Criado em: {{ $ticket->createdAt }}</p>
            <p>Atualizado: {{ $ticket->updatedAt }}</p>
        </div>
    </div>

    {{-- Right panel: chat --}}
    <div class="flex-1 flex flex-col">

        {{-- Chat header --}}
        <div class="flex items-center justify-between px-6 py-4 border-b border-zinc-800">
            <div class="flex items-center gap-3">
                <span
                    class="flex items-center gap-1.5 text-xs font-medium text-emerald-400 bg-emerald-500/10 px-2.5 py-1 rounded-full">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span> CONECTADO LIVE
                </span>
                <span class="text-white font-medium">{{ $ticket->titulo}}</span>
            </div>
            <span class="text-xs font-medium text-red-400 bg-red-500/10 px-2.5 py-1 rounded-full">
                Urgente
            </span>
        </div>

        {{-- Messages --}}
        <div class="flex-1 overflow-y-auto px-6 py-6 space-y-5">
            @foreach ($messages as $message)
                @if ($message['from'] === 'agent')
                    <div class="flex items-end gap-3 justify-end">
                        <div class="max-w-lg">
                            <div class="bg-indigo-600 text-white rounded-2xl rounded-br-sm px-4 py-3 text-sm">
                                {{ $message['text'] }}

                                @if (isset($message['attachment']))
                                    <div
                                        class="flex items-center gap-2 bg-indigo-500/50 rounded-lg px-3 py-2 mt-3 text-xs">
                                        <flux:icon name="document-text" class="w-4 h-4"/>
                                        <span class="flex-1">{{ $message['attachment']['name'] }}</span>
                                        <span class="text-indigo-200">({{ $message['attachment']['size'] }})</span>
                                    </div>
                                @endif
                            </div>
                            <p class="text-xs text-zinc-500 mt-1 text-right">{{ $message['author'] }}
                                • {{ $message['time'] }}</p>
                        </div>
                        <div class="relative shrink-0">
                            <div
                                class="w-8 h-8 rounded-full bg-indigo-500/20 text-indigo-300 text-xs font-semibold flex items-center justify-center">
                                {{ $message['initials'] }}
                            </div>
                            <span
                                class="absolute bottom-0 right-0 w-2 h-2 rounded-full bg-emerald-400 border-2 border-zinc-950"></span>
                        </div>
                    </div>
                @else
                    <div class="flex items-end gap-3">
                        <div class="relative shrink-0">
                            <div
                                class="w-8 h-8 rounded-full bg-zinc-700 text-zinc-200 text-xs font-semibold flex items-center justify-center">
                                {{ $message['initials'] }}
                            </div>
                            <span
                                class="absolute bottom-0 right-0 w-2 h-2 rounded-full bg-emerald-400 border-2 border-zinc-950"></span>
                        </div>
                        <div class="max-w-lg">
                            <div class="bg-zinc-800 text-zinc-200 rounded-2xl rounded-bl-sm px-4 py-3 text-sm">
                                {{ $message['text'] }}
                            </div>
                            <p class="text-xs text-zinc-500 mt-1">{{ $message['author'] }} • {{ $message['time'] }}</p>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>

        {{-- Reply input --}}
        <form wire:submit="sendReply" class="flex items-center gap-3 px-6 py-4 border-t border-zinc-800">
            <button type="button" class="text-zinc-500 hover:text-white">
                <flux:icon name="paper-clip" class="w-5 h-5"/>
            </button>
            <flux:input
                class="flex-1"
                placeholder="Escreva uma resposta para {{ $customer['name'] }}..."
                wire:model="reply"
            />
            <button type="submit"
                    class="w-9 h-9 rounded-full bg-indigo-600 hover:bg-indigo-500 flex items-center justify-center shrink-0">
                <flux:icon name="paper-airplane" class="w-4 h-4 text-white"/>
            </button>
        </form>
    </div>
</div>
