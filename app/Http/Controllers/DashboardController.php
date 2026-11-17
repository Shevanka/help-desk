<?php

namespace App\Http\Controllers;

use App\Enums\TicketStatus;
use App\Models\Category;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();

        // One grouped query for all status counters of the visible tickets.
        $byStatus = Ticket::query()
            ->visibleTo($user)
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $count = fn (TicketStatus $s) => (int) ($byStatus[$s->value] ?? 0);

        $stats = [
            'total' => (int) $byStatus->sum(),
            'open' => $count(TicketStatus::Open),
            'in_progress' => $count(TicketStatus::InProgress),
            'resolved' => $count(TicketStatus::Resolved),
            'closed' => $count(TicketStatus::Closed),
        ];

        $recent = Ticket::query()
            ->visibleTo($user)
            ->with(['category', 'requester', 'technician'])
            ->latest('updated_at')
            ->limit(5)
            ->get();

        if ($user->isAdmin()) {
            return view('dashboard.admin', [
                'stats' => $stats + [
                    'technicians' => User::technicians()->count(),
                    'unassigned' => Ticket::whereNull('technician_id')->where('status', '!=', TicketStatus::Closed->value)->count(),
                ],
                'recent' => $recent,
                'categories' => Category::withCount('tickets')->orderByDesc('tickets_count')->get(),
            ]);
        }

        if ($user->isTechnician()) {
            return view('dashboard.technician', ['stats' => $stats, 'recent' => $recent]);
        }

        return view('dashboard.requester', ['stats' => $stats, 'recent' => $recent]);
    }
}
