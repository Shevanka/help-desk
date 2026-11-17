<?php

namespace App\Http\Controllers\Admin;

use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Services\ReportService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $report = $this->report($request);
        $byStatus = $report->byStatus();
        $byPriority = $report->byPriority();

        return view('admin.reports.index', [
            'from' => $report->from(),
            'to' => $report->to(),
            'summary' => $report->summary($byStatus),
            'byStatus' => $byStatus,
            'byPriority' => $byPriority,
            'byCategory' => $report->byCategory(),
            'perPeriod' => $report->perPeriod(),
            'perTechnician' => $report->perTechnician(),
        ]);
    }

    /**
     * CSV of all tickets created in the selected range (optionally filtered).
     * Streamed in chunks so large exports never load everything into memory.
     */
    public function export(Request $request): StreamedResponse
    {
        $report = $this->report($request);
        $filters = $request->validate([
            'status' => ['nullable', Rule::enum(TicketStatus::class)],
            'category' => ['nullable', 'integer', 'exists:categories,id'],
            'priority' => ['nullable', Rule::enum(TicketPriority::class)],
            'technician' => ['nullable', 'string', 'max:20'],
        ]) + ['from' => $report->from()->toDateString(), 'to' => $report->to()->toDateString()];

        $filename = sprintf('helpdesk-tickets_%s_%s.csv', $report->from()->format('Ymd'), $report->to()->format('Ymd'));

        return response()->streamDownload(function () use ($filters) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF"); // UTF-8 BOM so Excel shows accents correctly

            fputcsv($out, ['Nomor Tiket', 'Subject', 'Kategori', 'Prioritas', 'Status', 'Pelapor', 'Email Pelapor',
                'Teknisi', 'Dibuat', 'Diselesaikan', 'Ditutup', 'Durasi Penyelesaian (jam)', 'Jumlah Komentar']);

            Ticket::query()
                ->filter(array_filter($filters))
                ->with(['category:id,name', 'requester:id,name,email', 'technician:id,name'])
                ->withCount('comments')
                ->orderBy('id')
                ->chunkById(500, function ($tickets) use ($out) {
                    foreach ($tickets as $t) {
                        fputcsv($out, [
                            $t->ticket_number,
                            $this->safeCell($t->subject),
                            $t->category->name,
                            $t->priority->label(),
                            $t->status->label(),
                            $this->safeCell($t->requester->name),
                            $t->requester->email,
                            $t->technician ? $this->safeCell($t->technician->name) : '',
                            $t->created_at->format('Y-m-d H:i'),
                            $t->resolved_at?->format('Y-m-d H:i') ?? '',
                            $t->closed_at?->format('Y-m-d H:i') ?? '',
                            $t->resolved_at ? round($t->created_at->diffInMinutes($t->resolved_at) / 60, 1) : '',
                            $t->comments_count,
                        ]);
                    }
                });

            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    private function report(Request $request): ReportService
    {
        $dates = $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
        ]);

        return ReportService::forRange($dates['from'] ?? null, $dates['to'] ?? null);
    }

    /** Prevent CSV/formula injection when the file is opened in a spreadsheet. */
    private function safeCell(string $value): string
    {
        return preg_match('/^[=+\-@\t\r]/', $value) ? "'".$value : $value;
    }
}
