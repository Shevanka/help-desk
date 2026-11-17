<?php

namespace App\Policies;

use App\Enums\TicketStatus;
use App\Models\Ticket;
use App\Models\User;

class TicketPolicy
{
    public function viewAny(User $user): bool
    {
        return true; // the list itself is scoped with Ticket::visibleTo()
    }

    public function view(User $user, Ticket $ticket): bool
    {
        return $user->isAdmin()
            || $ticket->user_id === $user->id
            || ($user->isTechnician() && $ticket->technician_id === $user->id);
    }

    public function create(User $user): bool
    {
        return $user->isRequester() || $user->isAdmin();
    }

    /** Edit subject / description / category. */
    public function update(User $user, Ticket $ticket): bool
    {
        if ($user->isAdmin()) {
            return $ticket->status !== TicketStatus::Closed;
        }

        return $ticket->user_id === $user->id && $ticket->status === TicketStatus::Open;
    }

    public function delete(User $user, Ticket $ticket): bool
    {
        return $user->isAdmin();
    }

    public function assign(User $user, Ticket $ticket): bool
    {
        return $user->isAdmin() && $ticket->status !== TicketStatus::Closed;
    }

    public function changePriority(User $user, Ticket $ticket): bool
    {
        if ($ticket->status === TicketStatus::Closed) {
            return false;
        }

        return $user->isAdmin()
            || ($user->isTechnician() && $ticket->technician_id === $user->id);
    }

    public function changeStatus(User $user, Ticket $ticket): bool
    {
        return $this->allowedStatuses($user, $ticket) !== [];
    }

    public function comment(User $user, Ticket $ticket): bool
    {
        return $this->view($user, $ticket) && $ticket->status !== TicketStatus::Closed;
    }

    /**
     * Status workflow: Open → In Progress → Resolved → Closed.
     * - Admin: any other status (incl. reopening).
     * - Assigned technician: Open → In Progress, In Progress → Resolved.
     * - Requester (owner): Resolved → Closed.
     *
     * @return list<TicketStatus>
     */
    public function allowedStatuses(User $user, Ticket $ticket): array
    {
        $current = $ticket->status;

        if ($user->isAdmin()) {
            return array_values(array_filter(
                TicketStatus::cases(),
                fn (TicketStatus $s) => $s !== $current,
            ));
        }

        if ($user->isTechnician() && $ticket->technician_id === $user->id) {
            return match ($current) {
                TicketStatus::Open => [TicketStatus::InProgress],
                TicketStatus::InProgress => [TicketStatus::Resolved],
                default => [],
            };
        }

        if ($ticket->user_id === $user->id && $current === TicketStatus::Resolved) {
            return [TicketStatus::Closed];
        }

        return [];
    }
}
