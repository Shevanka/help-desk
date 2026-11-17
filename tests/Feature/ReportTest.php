<?php

namespace Tests\Feature;

use App\Enums\TicketStatus;
use App\Models\Category;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->admin()->create();
    }

    public function test_reports_are_admin_only(): void
    {
        foreach ([User::factory()->requester()->create(), User::factory()->technician()->create()] as $user) {
            $this->actingAs($user)->get(route('admin.reports.index'))->assertForbidden();
            $this->actingAs($user)->get(route('admin.reports.export'))->assertForbidden();
        }

        $this->actingAs($this->admin)->get(route('admin.reports.index'))->assertOk()->assertSee('Laporan & Statistik', false);
    }

    public function test_report_aggregates_by_status_priority_category_and_technician(): void
    {
        $network = Category::factory()->create(['name' => 'Network']);
        $hardware = Category::factory()->create(['name' => 'Hardware']);
        Category::factory()->create(['name' => 'Unused']);
        $budi = User::factory()->technician()->create(['name' => 'Budi']);

        Ticket::factory()->for($network)->assignedTo($budi)->status(TicketStatus::Resolved)->create(['priority' => 'high']);
        Ticket::factory()->for($network)->assignedTo($budi)->status(TicketStatus::InProgress)->create(['priority' => 'high']);
        Ticket::factory()->for($hardware)->create(['priority' => 'low']);

        $response = $this->actingAs($this->admin)->get(route('admin.reports.index'))->assertOk();

        $this->assertSame(['open' => 1, 'in_progress' => 1, 'resolved' => 1, 'closed' => 0], $response->viewData('byStatus'));
        $this->assertSame(['low' => 1, 'medium' => 0, 'high' => 2, 'critical' => 0], $response->viewData('byPriority'));

        $categories = collect($response->viewData('byCategory'))->pluck('total', 'name')->map(fn ($n) => (int) $n);
        $this->assertSame(['Network' => 2, 'Hardware' => 1, 'Unused' => 0], $categories->all());

        $tech = collect($response->viewData('perTechnician'))->firstWhere('name', 'Budi');
        $this->assertSame([2, 1, 1], [$tech->assigned, $tech->active, $tech->resolved]);

        $summary = $response->viewData('summary');
        $this->assertSame(3, $summary['total']);
        $this->assertSame(1, $summary['unassigned']);
        $this->assertEqualsWithDelta(33.3, $summary['resolution_rate'], 0.1);
    }

    public function test_date_range_filters_report_and_period_series(): void
    {
        $inRange = Ticket::factory()->create();
        $inRange->forceFill(['created_at' => '2026-03-10 09:00:00'])->save();
        $outRange = Ticket::factory()->create();
        $outRange->forceFill(['created_at' => '2026-01-05 09:00:00'])->save();

        $response = $this->actingAs($this->admin)->get(route('admin.reports.index', ['from' => '2026-03-01', 'to' => '2026-03-31']));

        $this->assertSame(1, $response->viewData('summary')['total']);
        $period = $response->viewData('perPeriod');
        $this->assertSame('harian', $period['granularity']);
        $this->assertCount(31, $period['labels']);
        $this->assertSame(1, array_sum($period['created']));
    }

    public function test_long_ranges_are_grouped_per_month(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.reports.index', ['from' => '2026-01-01', 'to' => '2026-06-30']));

        $period = $response->viewData('perPeriod');
        $this->assertSame('bulanan', $period['granularity']);
        $this->assertCount(6, $period['labels']);
    }

    public function test_invalid_date_range_is_rejected(): void
    {
        $this->actingAs($this->admin)
            ->get(route('admin.reports.index', ['from' => '2026-05-10', 'to' => '2026-05-01']))
            ->assertSessionHasErrors('to');
    }

    public function test_csv_export_contains_tickets_in_range(): void
    {
        $tech = User::factory()->technician()->create(['name' => 'Sari']);
        $ticket = Ticket::factory()->assignedTo($tech)->status(TicketStatus::Resolved)
            ->create(['subject' => 'Proyektor rusak', 'priority' => 'critical']);
        $old = Ticket::factory()->create(['subject' => 'Tiket lama sekali']);
        $old->forceFill(['created_at' => now()->subYear()])->save();

        $response = $this->actingAs($this->admin)->get(route('admin.reports.export'));

        $response->assertOk();
        $this->assertStringContainsString('text/csv', $response->headers->get('Content-Type'));
        $this->assertStringContainsString('attachment; filename=helpdesk-tickets_', $response->headers->get('Content-Disposition'));

        $csv = $response->streamedContent();
        $this->assertStringStartsWith("\xEF\xBB\xBF", $csv);
        $rows = array_map('str_getcsv', array_filter(explode("\n", trim(substr($csv, 3)))));
        $this->assertSame('Nomor Tiket', $rows[0][0]);
        $this->assertCount(2, $rows); // header + 1 ticket in default 30-day range
        $this->assertSame([$ticket->ticket_number, 'Proyektor rusak'], array_slice($rows[1], 0, 2));
        $this->assertSame(['Critical', 'Resolved'], [$rows[1][3], $rows[1][4]]);
        $this->assertSame('Sari', $rows[1][7]);
    }

    public function test_csv_export_neutralises_spreadsheet_formulas(): void
    {
        Ticket::factory()->create(['subject' => '=HYPERLINK("http://evil.test","klik")']);

        $csv = $this->actingAs($this->admin)->get(route('admin.reports.export'))->streamedContent();

        $this->assertStringContainsString("'=HYPERLINK", $csv);
    }

    public function test_admin_dashboard_renders_charts(): void
    {
        Ticket::factory()->create();

        $this->actingAs($this->admin)->get('/dashboard')
            ->assertOk()
            ->assertSee('data-chart', false)
            ->assertSee('Tiket berdasarkan Status');
    }
}
