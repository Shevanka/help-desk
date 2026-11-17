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
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TicketController extends Controller
{
    public function __construct(private readonly TicketService $tickets)
    {
    }

    public function index(Request $request): View
    {
        return $this->listing($request, history: false);
    }

    /** Technician "Ticket History": tickets they already resolved / closed. */
    public function history(Request $request): View
    {
        return $this->listing($request, history: true);
    }

    private function listing(Request $request, bool $history): View
    {
        $user = $request->user();
        $filters = $this->validatedFilters($request);

        if ($history) {
            $filters['status'] = in_array($filters['status'] ?? null, [TicketStatus::Resolved->value, TicketStatus::Closed->value], true)
                ? $filters['status']
                : [TicketStatus::Resolved->value, TicketStatus::Closed->value];
        }

        if (! $user->isAdmin()) {
            unset($filters['technician']); // only admins may filter by technician
        }

        $tickets = Ticket::query()
            ->visibleTo($user)
            ->filter($filters)
            ->with(['category', 'requester', 'technician'])
            ->when($history, fn ($q) => $q->orderByDesc('resolved_at'), fn ($q) => $q->latest())
            ->paginate(10)
            ->withQueryString();

        return view('tickets.index', [
            'tickets' => $tickets,
            'filters' => $filters,
            'history' => $history,
            'categories' => Category::orderBy('name')->get(['id', 'name']),
            'priorities' => TicketPriority::cases(),
            'technicians' => $user->isAdmin() ? User::technicians()->orderBy('name')->get(['id', 'name']) : collect(),
        ]);
    }

    /** @return array<string, mixed> */
    private function validatedFilters(Request $request): array
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', Rule::enum(TicketStatus::class)],
            'category' => ['nullable', 'integer', 'exists:categories,id'],
            'priority' => ['nullable', Rule::enum(TicketPriority::class)],
            'technician' => ['nullable', 'string', 'max:20'], // user id or "none"
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
        ]);

        return array_filter($filters, fn ($v) => $v !== null && $v !== '');
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
        $ticket = $this->tickets->create(
            $request->user(),
            $request->safe()->except('attachment'),
            $request->file('attachment'),
        );

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
        $this->tickets->update($ticket, $request->user(), $request->safe()->only(['subject', 'description', 'category_id']));
        $this->tickets->updateAttachment($ticket, $request->user(), $request->file('attachment'), $request->boolean('remove_attachment'));

        return redirect()->route('tickets.show', $ticket)->with('status', 'Tiket berhasil diperbarui.');
    }

    public function destroy(Ticket $ticket): RedirectResponse
    {
        Gate::authorize('delete', $ticket);

        $number = $ticket->ticket_number;
        $this->tickets->delete($ticket);

        return redirect()->route('tickets.index')->with('status', "Tiket {$number} telah dihapus.");
    }

    /**
     * Serve the private attachment to authorised users only.
     * Images are shown inline; everything else is forced to download.
     */
    public function attachment(Request $request, Ticket $ticket): StreamedResponse
    {
        Gate::authorize('view', $ticket);

        $disk = Storage::disk(TicketService::ATTACHMENT_DISK);
        abort_unless($ticket->hasAttachment() && $disk->exists($ticket->attachment), 404, 'Lampiran tidak ditemukan.');

        $name = $ticket->attachment_name ?: basename($ticket->attachment);
        $headers = ['X-Content-Type-Options' => 'nosniff', 'Cache-Control' => 'private, max-age=0'];

        if ($ticket->attachmentIsImage() && ! $request->boolean('download')) {
            return $disk->response($ticket->attachment, $name, $headers, 'inline');
        }

        return $disk->download($ticket->attachment, $name, $headers);
    }
}
