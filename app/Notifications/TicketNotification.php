<?php

namespace App\Notifications;

use App\Models\Ticket;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Base class for ticket e-mails: same subject prefix, greeting and "Lihat Tiket" button.
 */
abstract class TicketNotification extends Notification
{
    public function __construct(public readonly Ticket $ticket) {}

    /** @return array<int, string> */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    protected function baseMessage(object $notifiable, string $title): MailMessage
    {
        return (new MailMessage)
            ->subject("[{$this->ticket->ticket_number}] {$title}")
            ->greeting("Halo, {$notifiable->name}")
            ->line("**Tiket:** {$this->ticket->ticket_number} — {$this->ticket->subject}");
    }

    protected function withTicketButton(MailMessage $message): MailMessage
    {
        return $message
            ->action('Lihat Tiket', route('tickets.show', $this->ticket))
            ->salutation('Salam, Tim '.config('app.name'));
    }
}
