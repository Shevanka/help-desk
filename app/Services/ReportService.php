<?php

namespace App\Services;

use App\Enums\Role;
use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use Carbon\CarbonPeriod;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Aggregated ticket statistics for the Reports page and the admin dashboard.
 * Uses the Query Builder (no model hydration) so each chart is one GROUP BY query.
 */
class ReportService
{
    /** Ranges up to this many days are grouped per day, longer ones per month. */
    public const DAILY_LIMIT_DAYS = 62;

    public function __construct(
        private readonly Carbon $from,
        private readonly Carbon $to,
    ) {
    }

    public static function forRange(?string $from, ?string $to): self
    {
        $end = $to ? Carbon::parse($to)->endOfDay() : now()->endOfDay();
        $start = $from ? Carbon::parse($from)->startOfDay() : $end->copy()->subDays(29)->startOfDay();

        return new self($start, $end);
    }

    public function from(): Carbon
    {
        return $this->from;
    }

    public function to(): Carbon
    {
        return $this->to;
    }

    /** Base query: tickets created inside the selected range. */
    private function tickets(): Builder
    {
        return DB::table('tickets')->whereBetween('tickets.created_at', [$this->from, $this->to]);
    }

    /** @return array<string, int> keyed by every status value (zero-filled) */
    public function byStatus(): array
    {
        $rows = $this->tickets()->selectRaw('status, COUNT(*) AS total')->groupBy('status')->pluck('total', 'status');

        return collect(TicketStatus::cases())
            ->mapWithKeys(fn (TicketStatus $s) => [$s->value => (int) ($rows[$s->value] ?? 0)])
            ->all();
    }

    /** @return array<string, int> */
    public function byPriority(): array
    {
        $rows = $this->tickets()->selectRaw('priority, COUNT(*) AS total')->groupBy('priority')->pluck('total', 'priority');

        return collect(TicketPriority::cases())
            ->mapWithKeys(fn (TicketPriority $p) => [$p->value => (int) ($rows[$p->value] ?? 0)])
            ->all();
    }

    /** @return list<object{name:string, is_active:int, total:int}> includes categories with zero tickets */
    public function byCategory(): array
    {
        return DB::table('categories')
            ->leftJoin('tickets', function ($join) {
                $join->on('tickets.category_id', '=', 'categories.id')
                    ->whereBetween('tickets.created_at', [$this->from, $this->to]);
            })
            ->groupBy('categories.id', 'categories.name', 'categories.is_active')
            ->orderByDesc('total')
            ->orderBy('categories.name')
            ->get(['categories.name', 'categories.is_active', DB::raw('COUNT(tickets.id) AS total')])
            ->all();
    }

    /**
     * Ticket count per day (or per month for long ranges), gaps filled with 0.
     *
     * @return array{granularity:string, labels:list<string>, created:list<int>, resolved:list<int>}
     */
    public function perPeriod(): array
    {
        $daily = $this->from->diffInDays($this->to) < self::DAILY_LIMIT_DAYS;
        $sqlFormat = $daily ? '%Y-%m-%d' : '%Y-%m';
        $phpFormat = $daily ? 'Y-m-d' : 'Y-m';

        $created = $this->tickets()
            ->selectRaw("DATE_FORMAT(created_at, '{$sqlFormat}') AS period, COUNT(*) AS total")
            ->groupBy('period')
            ->pluck('total', 'period');

        $resolved = DB::table('tickets')
            ->whereBetween('resolved_at', [$this->from, $this->to])
            ->selectRaw("DATE_FORMAT(resolved_at, '{$sqlFormat}') AS period, COUNT(*) AS total")
            ->groupBy('period')
            ->pluck('total', 'period');

        $period = CarbonPeriod::create(
            $daily ? $this->from->copy()->startOfDay() : $this->from->copy()->startOfMonth(),
            $daily ? '1 day' : '1 month',
            $this->to,
        );

        $labels = $createdSeries = $resolvedSeries = [];
        foreach ($period as $date) {
            $key = $date->format($phpFormat);
            $labels[] = $daily ? $date->translatedFormat('d M') : $date->translatedFormat('M Y');
            $createdSeries[] = (int) ($created[$key] ?? 0);
            $resolvedSeries[] = (int) ($resolved[$key] ?? 0);
        }

        return [
            'granularity' => $daily ? 'harian' : 'bulanan',
            'labels' => $labels,
            'created' => $createdSeries,
            'resolved' => $resolvedSeries,
        ];
    }

    /**
     * Workload and performance per technician for tickets created in range.
     *
     * @return list<object{id:int, name:string, assigned:int, active:int, resolved:int, avg_hours:float|null}>
     */
    public function perTechnician(): array
    {
        $done = [TicketStatus::Resolved->value, TicketStatus::Closed->value];
        $active = [TicketStatus::Open->value, TicketStatus::InProgress->value];

        return DB::table('users')
            ->where('users.role', Role::Technician->value)
            ->leftJoin('tickets', function ($join) {
                $join->on('tickets.technician_id', '=', 'users.id')
                    ->whereBetween('tickets.created_at', [$this->from, $this->to]);
            })
            ->groupBy('users.id', 'users.name')
            ->orderByDesc('assigned')
            ->orderBy('users.name')
            ->select('users.id', 'users.name')
            ->selectRaw('COUNT(tickets.id) AS assigned')
            ->selectRaw('SUM(CASE WHEN tickets.status IN (?, ?) THEN 1 ELSE 0 END) AS active', $active)
            ->selectRaw('SUM(CASE WHEN tickets.status IN (?, ?) THEN 1 ELSE 0 END) AS resolved', $done)
            ->selectRaw('AVG(CASE WHEN tickets.resolved_at IS NOT NULL THEN TIMESTAMPDIFF(MINUTE, tickets.created_at, tickets.resolved_at) END) / 60 AS avg_hours')
            ->get()
            ->map(function ($row) {
                $row->assigned = (int) $row->assigned;
                $row->active = (int) $row->active;
                $row->resolved = (int) $row->resolved;
                $row->avg_hours = $row->avg_hours !== null ? round((float) $row->avg_hours, 1) : null;

                return $row;
            })
            ->all();
    }

    /** @return array{total:int, unassigned:int, resolution_rate:float, avg_hours:float|null} */
    public function summary(array $byStatus): array
    {
        $total = array_sum($byStatus);
        $done = $byStatus[TicketStatus::Resolved->value] + $byStatus[TicketStatus::Closed->value];
        $avgMinutes = $this->tickets()->whereNotNull('resolved_at')
            ->avg(DB::raw('TIMESTAMPDIFF(MINUTE, created_at, resolved_at)'));

        return [
            'total' => $total,
            'unassigned' => $this->tickets()->whereNull('technician_id')->count(),
            'resolution_rate' => $total ? round($done / $total * 100, 1) : 0.0,
            'avg_hours' => $avgMinutes !== null ? round($avgMinutes / 60, 1) : null,
        ];
    }
}
