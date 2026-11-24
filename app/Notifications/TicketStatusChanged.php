<?php

namespace App\Notifications;

use App\Enums\TicketStatus;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Notifications\Messages\MailMessage;

/** Sent to the requester when someone else changes the ticket status. */
class TicketStatusChanged extends TicketNotification
{
    public function __construct(
        Ticket $ticket,
        public readonly TicketStatus $oldStatus,
        public readonly TicketStatus $newStatus,
        public readonly User $actor,
        public readonly ?string $note = null,
    ) {
        parent::__construct($ticket);
    }

    public function toMail(object $notifiable): MailMessage
    {
        $message = $this->baseMessage($notifiable, "Status berubah menjadi {$this->newStatus->label()}")
            ->line("Status tiket diubah dari **{$this->oldStatus->label()}** menjadi **{$this->newStatus->label()}** oleh {$this->actor->name}.");

        if (filled($this->note)) {
            $message->line("**Catatan:** {$this->note}");
        }

        if ($this->newStatus === TicketStatus::Resolved) {
            $message->line('Jika masalah masih terjadi, silakan balas melalui komentar di halaman tiket.');
        }

        return $this->withTicketButton($message);
    }
}
