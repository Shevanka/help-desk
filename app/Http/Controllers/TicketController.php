<?php

namespace App\Http\Controllers;

use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Http\Requests\StoreTicketRequest;
use App\Http\Requests\UpdateTicketRequest;
use App\Models\Category;
use App\Models\Ticket;
use App\Models\User;
use App\Services\TicketService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TicketController extends Controller
{
    public function __construct(private readonly TicketService $tickets)
    {
    }

    public function index(Request $request): View
    {
        $user = $request->user();

        $filters = $request->validate([
            'status' => ['nullable', Rule::enum(TicketStatus::class)],
            'technician' => ['nullable', 'string'], // user id, or "none" for unassigned (admin only)
        ]);

        $query = Ticket::query()
            ->visibleTo($user)
            ->with(['category', 'requester', 'technician'])
            ->when($filters['status'] ?? null, fn ($q, $status) => $q->where('status', $status));

        if ($user->isAdmin() && ! empty($filters['technician'])) {
            $filters['technician'] === 'none'
                ? $query->whereNull('technician_id')
                : $query->where('technician_id', (int) $filters['technician']);
        }

        return view('tickets.index', [
            'tickets' => $query->latest()->paginate(10)->withQueryString(),
            'filters' => $filters,
            'technicians' => $user->isAdmin() ? User::technicians()->orderBy('name')->get(['id', 'name']) : collect(),
        ]);
    }

    public function create(): View
    {
        Gate::authorize('create', Ticket::class);

        return view('tickets.create', [
            'categories' => Category::active()->orderBy('name')->get(),
            'priorities' => TicketPriority::cases(),
        ]);
    }

    public function store(StoreTicketRequest $request): RedirectResponse
    {
        $ticket = $this->tickets->create($request->user(), $request->validated());

        return redirect()
            ->route('tickets.show', $ticket)
            ->with('status', "Tiket {$ticket->ticket_number} berhasil dibuat.");
    }

    public function show(Request $request, Ticket $ticket): View
    {
        Gate::authorize('view', $ticket);

        $ticket->load(['category', 'requester', 'technician', 'comments.user', 'activities.user']);
        $user = $request->user();

        return view('tickets.show', [
            'ticket' => $ticket,
            'allowedStatuses' => Gate::getPolicyFor($ticket)->allowedStatuses($user, $ticket),
            'technicians' => $user->can('assign', $ticket)
                ? User::technicians()->withCount(['assignedTickets as active_tickets_count' => fn ($q) => $q->whereIn('status', [TicketStatus::Open->value, TicketStatus::InProgress->value])])->orderBy('name')->get()
                : collect(),
            'priorities' => TicketPriority::cases(),
        ]);
    }

    public function edit(Ticket $ticket): View
    {
        Gate::authorize('update', $ticket);

        return view('tickets.edit', [
            'ticket' => $ticket,
            'categories' => Category::query()
                ->where(fn ($q) => $q->where('is_active', true)->orWhere('id', $ticket->category_id))
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function update(UpdateTicketRequest $request, Ticket $ticket): RedirectResponse
    {
        $this->tickets->update($ticket, $request->user(), $request->validated());

        return redirect()->route('tickets.show', $ticket)->with('status', 'Tiket berhasil diperbarui.');
    }

    public function destroy(Ticket $ticket): RedirectResponse
    {
        Gate::authorize('delete', $ticket);

        $number = $ticket->ticket_number;
        $ticket->delete();

        return redirect()->route('tickets.index')->with('status', "Tiket {$number} telah dihapus.");
    }
}
