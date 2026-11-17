<?php

namespace App\Services;

use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Models\Comment;
use App\Models\Ticket;
use App\Models\TicketActivity;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Business logic for tickets. Every mutation runs inside a DB transaction
 * together with its activity-log entry so the history never drifts.
 */
class TicketService
{
    /** @param array{subject:string, description:string, category_id:int, priority:string} $data */
    public function create(User $reporter, array $data): Ticket
    {
        return DB::transaction(function () use ($reporter, $data) {
            $ticket = Ticket::create([
                'ticket_number' => 'TMP-'.Str::random(20), // replaced right below
                'user_id' => $reporter->id,
                'category_id' => $data['category_id'],
                'subject' => $data['subject'],
                'description' => $data['description'],
                'priority' => $data['priority'],
                'status' => TicketStatus::Open,
            ]);

            // Derive the readable number from the auto-increment id, so it is always unique.
            $ticket->ticket_number = sprintf('TCK-%s-%05d', $ticket->created_at->format('Ymd'), $ticket->id);
            $ticket->save();

            $this->log($ticket, $reporter, TicketActivity::CREATED, null, TicketStatus::Open->label(), 'Ticket created');

            return $ticket;
        });
    }

    /** @param array{subject:string, description:string, category_id:int} $data */
    public function update(Ticket $ticket, User $actor, array $data): Ticket
    {
        return DB::transaction(function () use ($ticket, $actor, $data) {
            $ticket->fill($data);
            $changed = array_keys($ticket->getDirty());

            if ($changed !== []) {
                $ticket->save();
                $this->log($ticket, $actor, TicketActivity::UPDATED, null, null,
                    'Ticket details updated ('.implode(', ', $changed).')');
            }

            return $ticket;
        });
    }

    public function assign(Ticket $ticket, User $actor, ?User $technician): Ticket
    {
        return DB::transaction(function () use ($ticket, $actor, $technician) {
            $old = $ticket->technician;

            if ($old?->id === $technician?->id) {
                return $ticket;
            }

            $ticket->technician()->associate($technician);
            $ticket->save();

            if ($technician === null) {
                $description = "Technician {$old->name} unassigned";
            } elseif ($old === null) {
                $description = "Ticket assigned to {$technician->name}";
            } else {
                $description = "Ticket reassigned from {$old->name} to {$technician->name}";
            }

            $this->log($ticket, $actor, TicketActivity::ASSIGNED, $old?->name, $technician?->name, $description);

            return $ticket;
        });
    }

    public function changeStatus(Ticket $ticket, User $actor, TicketStatus $status, ?string $note = null): Ticket
    {
        return DB::transaction(function () use ($ticket, $actor, $status, $note) {
            $old = $ticket->status;

            if ($old === $status) {
                return $ticket;
            }

            $ticket->status = $status;

            if ($status === TicketStatus::Resolved) {
                $ticket->resolved_at = now();
                $ticket->closed_at = null;
            } elseif ($status === TicketStatus::Closed) {
                $ticket->closed_at = now();
                $ticket->resolved_at ??= now();
            } else {
                // Reopening clears completion timestamps.
                $ticket->resolved_at = null;
                $ticket->closed_at = null;
            }

            $ticket->save();

            $description = match ($status) {
                TicketStatus::Resolved => 'Ticket resolved',
                TicketStatus::Closed => 'Ticket closed',
                default => "Status changed to {$status->label()}",
            };

            $this->log($ticket, $actor, TicketActivity::STATUS_CHANGED, $old->label(), $status->label(),
                $note ? "{$description}. Catatan: {$note}" : $description);

            return $ticket;
        });
    }

    public function changePriority(Ticket $ticket, User $actor, TicketPriority $priority): Ticket
    {
        return DB::transaction(function () use ($ticket, $actor, $priority) {
            $old = $ticket->priority;

            if ($old === $priority) {
                return $ticket;
            }

            $ticket->priority = $priority;
            $ticket->save();

            $this->log($ticket, $actor, TicketActivity::PRIORITY_CHANGED, $old->label(), $priority->label(),
                "Priority changed from {$old->label()} to {$priority->label()}");

            return $ticket;
        });
    }

    public function addComment(Ticket $ticket, User $author, string $message): Comment
    {
        return DB::transaction(function () use ($ticket, $author, $message) {
            $comment = $ticket->comments()->create([
                'user_id' => $author->id,
                'message' => $message,
            ]);

            $ticket->touch();

            $this->log($ticket, $author, TicketActivity::COMMENTED, null, null, 'Comment added');

            return $comment;
        });
    }

    private function log(Ticket $ticket, ?User $actor, string $action, ?string $old, ?string $new, ?string $description): void
    {
        $ticket->activities()->create([
            'user_id' => $actor?->id,
            'action' => $action,
            'old_value' => $old,
            'new_value' => $new,
            'description' => $description,
        ]);
    }
}
