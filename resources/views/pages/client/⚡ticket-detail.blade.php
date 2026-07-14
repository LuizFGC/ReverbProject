<?php

use App\Models\Ticket;
use App\Models\User;
use App\Services\TicketService;
use Illuminate\Support\Collection;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;

new #[Layout('layouts.client')]
class extends Component {
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
<div class="flex h-[calc(100vh-2rem)]">

    {{-- Left: conversation --}}
    <div class="flex-1 flex flex-col">

        {{-- Header --}}
        <div class="flex items-center justify-between px-6 py-4 border-b border-zinc-800">
            <div class="flex items-center gap-4">
                <a href="/portal" wire:navigate
                   class="flex items-center gap-1.5 text-zinc-400 hover:text-white text-sm">
                    <flux:icon name="arrow-left" class="w-4 h-4"/>
                    Voltar
                </a>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="text-indigo-400 font-semibold text-sm">{{ $ticket->id }}</span>
                        <span class="text-white font-medium">{{ $ticket->titulo }}</span>
                    </div>
                    <p class="text-xs text-zinc-500">
                        Criado em: {{ $ticket->created_at->format('d/m/Y') }} •
                        Categoria: {{ ucfirst($ticket->categoria) }}
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                {{--                <span class="text-xs font-semibold px-3 py-1.5 rounded-full uppercase {{ $this->statusMeta['badge'] }}">--}}
                {{--                    {{ $this->statusMeta['label'] }}--}}
                {{--                </span>--}}

                {{--                @if ($ticket->status !== 'Resolvido')--}}
                {{--                    <flux:button variant="primary" size="sm" icon="check-circle" wire:click="markAsResolved">--}}
                {{--                        Marcar como Resolvido--}}
                {{--                    </flux:button>--}}
                {{--                @endif--}}
            </div>
        </div>

        {{-- Initial description --}}
        <div class="px-6 pt-6">
            <div class="bg-indigo-500/5 border border-indigo-500/30 rounded-xl p-4">
                <p class="text-xs font-semibold tracking-wide text-indigo-400 mb-2">DESCRIÇÃO INICIAL DO PROBLEMA</p>
                <p class="text-sm text-zinc-300">{{ $ticket->descricao }}</p>
            </div>
        </div>

        {{-- Messages --}}
        <div class="flex-1 overflow-y-auto px-6 py-6 space-y-5">
            <div class="flex justify-center">
                <span
                    class="text-xs font-medium text-zinc-500 bg-zinc-900 border border-zinc-800 rounded-full px-3 py-1">
                    Início do Histórico de Atendimento
                </span>
            </div>

            @if(isset($ticket->messages))
                @foreach ($ticket->messages as $message)
                    @if ($message->type === 'system')
                        <div class="flex justify-center">
                        <span
                            class="text-xs font-medium text-indigo-400 bg-indigo-500/10 border border-indigo-500/20 rounded-lg px-4 py-2 text-center max-w-md">
                            ⚙️ {{ $message->body }}
                        </span>
                        </div>
                    @elseif ($message->author_type === 'customer')
                        <div class="flex items-end gap-3 justify-end">
                            <div class="max-w-lg">
                                <div class="bg-indigo-600 text-white rounded-2xl rounded-br-sm px-4 py-3 text-sm">
                                    {{ $message->body }}
                                </div>
                                <p class="text-xs text-zinc-500 mt-1 text-right">
                                    {{ $message->author->name }} • {{ $message->created_at->format('H:i') }}
                                </p>
                            </div>
                            <div
                                class="w-8 h-8 rounded-full bg-indigo-500/20 text-indigo-300 text-xs font-semibold flex items-center justify-center shrink-0">
                                {{ $message->author->initials }}
                            </div>
                        </div>
                    @else
                        <div class="flex items-end gap-3">
                            <div
                                class="w-8 h-8 rounded-full bg-zinc-700 text-zinc-200 text-xs font-semibold flex items-center justify-center shrink-0">
                                {{ $message->author->initials }}
                            </div>
                            <div class="max-w-lg">
                                <p class="text-sm text-zinc-400 mb-1">
                                    <span class="text-white font-medium">{{ $message->author->name }}</span>
                                    • {{ $message->created_at->format('H:i') }}
                                </p>
                                <div class="bg-zinc-800 text-zinc-200 rounded-2xl rounded-bl-sm px-4 py-3 text-sm">
                                    {{ $message->body }}
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
            @endif
        </div>

        {{-- Reply input --}}
        <form wire:submit="sendReply" class="flex items-center gap-3 px-6 py-4 border-t border-zinc-800">
            <flux:input
                class="flex-1"
                placeholder="Digite sua resposta aqui para falar com o suporte..."
                wire:model="reply"
            />
            <flux:button type="submit" variant="primary" icon="paper-airplane"/>
        </form>
    </div>

    {{-- Right: sidebar with ticket details --}}
    <div class="w-80 shrink-0 border-l border-zinc-800 p-6 overflow-y-auto">
        <p class="text-xs font-medium tracking-wide text-zinc-500 mb-4">DETALHES DO CHAMADO</p>

        <div class="space-y-4">
            <div>
                <p class="text-sm text-zinc-500">Status</p>
                {{--                <span--}}
                {{--                    class="inline-block mt-1 text-xs font-semibold px-3 py-1 rounded-full uppercase {{ $this->statusMeta['badge'] }}">--}}
                {{--                    {{ $this->statusMeta['label'] }}--}}
                {{--                </span>--}}
            </div>

            <div>
                <p class="text-sm text-zinc-500">Prioridade</p>
                {{--                <p class="flex items-center gap-1.5 mt-1 font-medium {{ $this->priorityMeta['color'] }}">--}}
                {{--                    <flux:icon name="exclamation-triangle" class="w-4 h-4"/>--}}
                {{--                    {{ $this->priorityMeta['label'] }}--}}
                {{--                </p>--}}
            </div>

            <div>
                <p class="text-sm text-zinc-500">Categoria</p>
                <p class="text-white font-medium mt-1">{{ ucfirst($ticket->categoria) }}</p>
            </div>
        </div>

        <div class="border-t border-zinc-800 mt-6 pt-6">
            <p class="text-xs font-medium tracking-wide text-zinc-500 mb-3">AGENTE ATRIBUÍDO</p>

            @if ($ticket->assigned_to)
                <div class="flex items-center gap-3">
                    <div
                        class="w-10 h-10 rounded-full bg-indigo-500/20 text-indigo-300 font-semibold flex items-center justify-center text-sm">
                        {{ $ticket->assigned_to }}
                    </div>
                    <div>
                        <p class="text-white font-medium">{{ $ticket->assigned_to }}</p>
                        <p class="text-xs text-zinc-500">{{ $ticket->assignedAgent->role_label ?? 'Agente de Suporte' }}</p>
                    </div>
                </div>
            @else
                <div class="text-center py-6">
                    <div class="w-10 h-10 rounded-full bg-zinc-800 flex items-center justify-center mx-auto mb-2">
                        <flux:icon name="user-group" class="w-5 h-5 text-zinc-500"/>
                    </div>
                    <p class="text-sm text-zinc-400 font-medium">Aguardando atribuição</p>
                    <p class="text-xs text-zinc-500">Nossa equipe responderá em instantes.</p>
                </div>
            @endif
        </div>

        <div class="border-t border-zinc-800 mt-6 pt-6">
            <p class="text-xs font-medium tracking-wide text-zinc-500 mb-3">INFORMAÇÕES ADICIONAIS</p>
            <div class="font-mono text-xs text-zinc-400 space-y-1">
                <p>ID: {{ $ticket->id }}</p>
                <p>Abertura: {{ $ticket->created_at->format('H:i:s \d\o \d\i\a d/m/Y') }}</p>
                <p>Atualização: {{ $ticket->updated_at->format('H:i:s') }}</p>
            </div>
        </div>
    </div>
</div>
