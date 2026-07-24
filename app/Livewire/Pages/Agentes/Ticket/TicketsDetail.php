<?php

namespace App\Livewire\Pages\Agentes\Ticket;


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

class TicketsDetail extends Component
{

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

    public function render()
    {
        return view('livewire.pages.agentes.ticket.tickets-detail');
    }
}
