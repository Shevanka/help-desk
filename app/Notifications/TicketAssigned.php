<?php

namespace App\Notifications;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Notifications\Messages\MailMessage;

/** Sent to a technician when a ticket is assigned to them. */
class TicketAssigned extends TicketNotification
{
    public function __construct(Ticket $ticket, public readonly User $actor)
    {
        parent::__construct($ticket);
    }

    public function toMail(object $notifiable): MailMessage
    {
        $ticket = $this->ticket;

        return $this->withTicketButton(
            $this->baseMessage($notifiable, 'Tiket baru ditugaskan kepada Anda')
                ->line("{$this->actor->name} menugaskan tiket ini kepada Anda.")
                ->line("**Prioritas:** {$ticket->priority->label()}")
                ->line("**Kategori:** {$ticket->category?->name}")
                ->line("**Pelapor:** {$ticket->requester?->name}")
        );
    }
}
