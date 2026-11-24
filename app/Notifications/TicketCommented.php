<?php

namespace App\Notifications;

use App\Models\Comment;
use App\Models\Ticket;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Str;

/** Sent to the other participants (requester / technician) when a comment is added. */
class TicketCommented extends TicketNotification
{
    public function __construct(Ticket $ticket, public readonly Comment $comment)
    {
        parent::__construct($ticket);
    }

    public function toMail(object $notifiable): MailMessage
    {
        return $this->withTicketButton(
            $this->baseMessage($notifiable, 'Komentar baru')
                ->line("{$this->comment->user->name} menambahkan komentar:")
                ->line('> '.Str::limit($this->comment->message, 500))
        );
    }
}
