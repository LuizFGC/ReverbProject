<?php

namespace App\Livewire\Pages\Cliente;

use App\Models\Message;
use App\Models\Ticket;
use App\Models\User;
use App\Services\MessageService;
use App\Services\TicketService;
use Illuminate\Support\Collection;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;

#[Layout('layouts.client')]

class TicketDetail extends Component
{

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

        abort_unless(
            $this->ticket->user_id === auth()->user()->sid,
            403
        );

    }

    public string $reply = '';


    public function sendReply(MessageService $service): void
    {

        $message = $service->createMessage(
            ticketId: $this->ticketId,
            userId: auth()->user()->sid,
            content: $this->reply
        );

        $message->load('user');

        $this->messages->push($message);


        $this->reply = '';
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
        return view('livewire.pages.cliente.ticket-detail');
    }
}
