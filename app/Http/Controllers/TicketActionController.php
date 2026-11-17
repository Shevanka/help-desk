<?php

namespace App\Http\Controllers;

use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Http\Requests\AssignTicketRequest;
use App\Http\Requests\StoreCommentRequest;
use App\Http\Requests\UpdateTicketStatusRequest;
use App\Models\Ticket;
use App\Models\User;
use App\Services\TicketService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

/**
 * Workflow actions on a single ticket: assignment, status, priority, comments.
 */
class TicketActionController extends Controller
{
    public function __construct(private readonly TicketService $tickets)
    {
    }

    public function assign(AssignTicketRequest $request, Ticket $ticket): RedirectResponse
    {
        $technician = $request->filled('technician_id')
            ? User::findOrFail($request->integer('technician_id'))
            : null;

        $this->tickets->assign($ticket, $request->user(), $technician);

        return back()->with('status', $technician
            ? "Tiket ditugaskan kepada {$technician->name}."
            : 'Penugasan teknisi dihapus.');
    }

    public function status(UpdateTicketStatusRequest $request, Ticket $ticket): RedirectResponse
    {
        $status = TicketStatus::from($request->validated('status'));

        $this->tickets->changeStatus($ticket, $request->user(), $status, $request->validated('note'));

        return back()->with('status', "Status tiket diubah menjadi {$status->label()}.");
    }

    public function priority(Request $request, Ticket $ticket): RedirectResponse
    {
        Gate::authorize('changePriority', $ticket);

        $data = $request->validate([
            'priority' => ['required', Rule::enum(TicketPriority::class)],
        ]);

        $priority = TicketPriority::from($data['priority']);
        $this->tickets->changePriority($ticket, $request->user(), $priority);

        return back()->with('status', "Prioritas tiket diubah menjadi {$priority->label()}.");
    }

    public function comment(StoreCommentRequest $request, Ticket $ticket): RedirectResponse
    {
        $this->tickets->addComment($ticket, $request->user(), $request->validated('message'));

        return redirect()
            ->to(route('tickets.show', $ticket).'#comments')
            ->with('status', 'Komentar berhasil ditambahkan.');
    }
}
