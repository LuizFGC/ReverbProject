<?php

use App\Models\Message;
use App\Models\Ticket;
use App\Models\User;
use App\Services\MessageService;
use App\Services\TicketService;
use App\Services\TimeService;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

new class extends Component {

    public string $ticketId;

    public ?Ticket $ticket = null;

    public string $status;

    public string $prioridade;

    public ?string $assigned_to = '';

    public User $customer;

    public Collection $messages;

    public function mount(string $id): void
    {

        $this->ticketId = $id;

        $service = app(TicketService::class);

        $data = $service->getTicketById($id);

        $this->ticket = $data['ticket'];
        $this->status = $this->ticket->status;
        $this->prioridade = $this->ticket->prioridade;
        $this->assigned_to = $this->ticket->assigned_to ?? '';
        $this->messages = $data['messages'];
        $this->customer = $data['customer'];



    }

    public string $reply = '';


    public function sendReply(MessageService $service): void
    {
//        $this->validate([
//            'reply' => 'required|string|max:5000'
//        ]);


        $message = $service->createMessage(
            ticketId: $this->ticketId,
            userId: auth()->user()->sid,
            content: $this->reply
        );

        $message->load('user');

        $this->messages->push($message);


        $this->reply = '';
    }

    public function updatedStatus($value, TicketService $service): void
    {
        $service->updateField(
            $this->ticket,
            'status',
            $value
        );
    }

    public function updatedPrioridade($value, TicketService $service): void
    {
        $service->updateField(
            $this->ticket,
            'prioridade',
            $value
        );
    }

    public function updatedAssignedTo($value, TicketService $service): void
    {

        $service->updateField(
            $this->ticket,
            'assigned_to',
            $value
        );
    }

    #[Computed(cache: false)]
    public function agentes()
    {
        return app(TimeService::class)->getAgentes();
    }

    #[On('echo-private:ticket.{ticketId},.MessageSent')]
    public function receiveMessage($event)
    {
            $message = Message::with('user')
            ->find($event['id']);

        if ($message && $message->user_id != auth()->user()->sid) {
            $this->messages->push($message);
        }
    }

};
?>
<div class="flex gap-6 h-[calc(85vh)] p-6">


    <flux:card class="w-80 shrink-0 overflow-y-auto bg-zinc-50 dark:bg-slate-900">

        <a href="/tickets" class="flex items-center gap-1.5 text-zinc-400 hover:text-white text-sm" wire:navigate >
            <flux:icon name="arrow-left" class="w-4 h-4"/>
        </a>

        <p class="text-xs font-medium tracking-wide text-zinc-500 uppercase">ID do Chamado</p>
        <p class="text-sm font-bold text-white mt-1">{{ $this->ticketId }}</p>

        <div class="border-t border-zinc-800 mt-5 pt-5 space-y-5">
            <div>
                <label class="block text-xs font-medium tracking-wide text-zinc-500 uppercase mb-1.5">Alterar
                    Status</label>
                <flux:select wire:model.live="status" size="sm"
                             class="[&>select]:bg-zinc-800/60 [&>select]:border-zinc-700 [&>select]:text-zinc-200 [&>select]:rounded-lg">
                    <flux:select.option value="Aberto">Aberto (Aguardando)</flux:select.option>
                    <flux:select.option value="Em Atendimento">Em Atendimento</flux:select.option>
                    <flux:select.option value="Resolvido">Resolvido</flux:select.option>
                    <flux:select.option value="Fechado">Fechado</flux:select.option>
                </flux:select>
            </div>

            <div>
                <label class="block text-xs font-medium tracking-wide text-zinc-500 uppercase mb-1.5">Alterar
                    Prioridade</label>
                <flux:select wire:model.live="prioridade" size="sm"
                             class="[&>select]:bg-zinc-800/60 [&>select]:border-zinc-700 [&>select]:text-zinc-200 [&>select]:rounded-lg">
                    <flux:select.option value="Baixa">Prioridade Baixa</flux:select.option>
                    <flux:select.option value="Media">Prioridade Média</flux:select.option>
                    <flux:select.option value="Alta">Prioridade Alta</flux:select.option>
                    <flux:select.option value="Urgente">Prioridade Urgente 🔺</flux:select.option>
                </flux:select>
            </div>

            <div>
                <label class="block text-xs font-medium tracking-wide text-zinc-500 uppercase mb-1.5">Categoria</label>
                <div class="bg-zinc-800/60 border border-zinc-700 rounded-lg px-3 py-2 text-sm text-zinc-300">
                    {{ $ticket->categoria }}
                </div>
            </div>

            <div>
                <label class="block text-xs font-medium tracking-wide text-zinc-500 uppercase mb-1.5">Agente
                    Responsável</label>
                <flux:select wire:model.live="assigned_to">
                    <flux:select.option value="">
                        Selecione
                    </flux:select.option>

                    @foreach ($this->agentes as $agent)
                        <flux:select.option value="{{ $agent['sid'] }}">
                            {{ $agent['name'] }} - {{ $agent['cargo']['nome'] }}
                        </flux:select.option>
                    @endforeach
                </flux:select>
            </div>
        </div>

        <div class="border-t border-zinc-800 mt-5 pt-5">
            <p class="text-xs font-medium tracking-wide text-zinc-500 uppercase mb-3">Dados do Cliente</p>

            @php
                $initials = collect(explode(' ', trim($customer['name'])))
                    ->filter()
                    ->map(fn ($n) => mb_strtoupper(mb_substr($n, 0, 1)))
                    ->take(2)
                    ->join('');
            @endphp

            <div class="flex items-center gap-3 mb-4">
                <div
                    class="w-10 h-10 rounded-full bg-zinc-700 text-zinc-200 font-semibold flex items-center justify-center text-sm shrink-0">
                    {{ $initials }}
                </div>
                <div>
                    <p class="text-white font-medium">{{ $customer['name'] }}</p>
                    <p class="text-xs text-indigo-400 font-medium">{{ strtoupper($customer['departamento']['nome'] ?? '') }}</p>
                </div>
            </div>

            <div class="space-y-2 text-sm text-zinc-400">
                <p class="flex items-center gap-2">
                    <flux:icon name="envelope" class="w-4 h-4 text-zinc-500 shrink-0"/>
                    <span>{{ $customer['email'] ?? '—' }}</span>
                </p>
                <p class="flex items-center gap-2">
                    <flux:icon name="user" class="w-4 h-4 text-zinc-500 shrink-0"/>
                    <span>{{ $customer['cargo']['nome'] ?? '—' }}</span>
                </p>
                <p class="flex items-center gap-2">
                    <flux:icon name="building-office-2" class="w-4 h-4 text-zinc-500 shrink-0"/>
                    <span>{{ $customer['departamento']['nome'] ?? '—' }}</span>
                </p>
            </div>
        </div>

        <div class="border-t border-zinc-800 mt-5 pt-5 text-xs text-zinc-500 space-y-1">
            <p>Abertura: {{ $ticket->created_at->format('H:i:s \d\o \d\i\a d/m/Y') }}</p>
            <p>Atualização: {{ $ticket->updated_at->format('H:i:s') }}</p>
        </div>
    </flux:card>

    {{--  chat --}}
    <flux:card class="flex-1 flex flex-col overflow-hidden !p-0 bg-zinc-50 dark:bg-slate-900">

        <div class="flex items-center justify-between px-6 py-4 border-b border-zinc-800">
            <div class="flex items-center gap-3">
                <span
                    class="flex items-center gap-1.5 text-xs font-medium text-emerald-400 bg-emerald-500/10 px-2.5 py-1 rounded-full">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span> CONECTADO LIVE
                </span>
                <span class="text-white font-medium">{{ $ticket->titulo }}</span>
            </div>
            <span class="text-xs font-medium text-red-400 bg-red-500/10 px-2.5 py-1 rounded-full">
                {{$ticket->prioridade}}
            </span>
        </div>

        {{--Mensagens--}}
        <div class="flex-1 overflow-y-auto px-6 py-6 space-y-5">
            @foreach ($messages as $message)
                @if ($message->user_id === auth()->user()->sid)
                    <div class="flex items-end gap-3 justify-end">
                        <div class="max-w-lg">
                            <div class="bg-indigo-600 text-white rounded-2xl rounded-br-sm px-4 py-3 text-sm">
                                {{ $message->content }}

                                @if (!empty($message->anexos))

                                    @foreach($message->anexos as $anexo)

                                        <div
                                            class="flex items-center gap-2 bg-indigo-500/50 rounded-lg px-3 py-2 mt-3 text-xs">

                                            <flux:icon name="document-text" class="w-4 h-4"/>

                                            <span class="flex-1">{{ $anexo['name'] }}    </span>

                                        </div>

                                    @endforeach

                                @endif
                            </div>
                            <p class="text-xs text-zinc-500 mt-1 text-right">
                                {{ $message->user->name }} • {{ $message->created_at->format('H:i') }}
                            </p>
                        </div>
                        <div class="relative shrink-0">
                            <div
                                class="w-8 h-8 rounded-full bg-indigo-500/20 text-indigo-300 text-xs font-semibold flex items-center justify-center">
                                {{auth()->user()->initials()}}
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
                                {{auth()->user()->initials()}}
                            </div>
                            <span
                                class="absolute bottom-0 right-0 w-2 h-2 rounded-full bg-emerald-400 border-2 border-zinc-950"></span>
                        </div>
                        <div class="max-w-lg">
                            <div class="bg-zinc-800 text-zinc-200 rounded-2xl rounded-bl-sm px-4 py-3 text-sm">
                                {{ $message->content }}
                            </div>
                            <p class="text-xs text-zinc-500 mt-1">
                                {{ $message->user->name }} • {{ $message->created_at->format('H:i') }}
                            </p>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>

        {{-- botao reply --}}
        <form wire:submit="sendReply" class="flex items-center gap-3 px-6 py-4 border-t border-zinc-800">
            <button type="button" class="text-zinc-500 hover:text-white shrink-0">
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
    </flux:card>
</div>
