<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TicketCriadoNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct( public string $userName, public string $ticketId)
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     */
//    public function toMail(object $notifiable): MailMessage
//    {
//        return (new MailMessage)
//            ->line('The introduction to the notification.')
//            ->action('Notification Action', url('/'))
//            ->line('Thank you for using our application!');
//    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'user_name' => $this->userName,
            'ticket_id' => $this->ticketId,
            'message' => "Ticket #{$this->ticketId} criado por {$this->userName}",
        ];
    }

//    public function toBroadcast(object $notifiable): BroadcastMessage
//    {
//        return new BroadcastMessage([
//            'user_name' => $this->userName,
//            'ticket_id' => $this->ticketId,
//            'message'   => "Ticket #{$this->ticketId} criado por {$this->userName}",
//        ]);
//    }
}
