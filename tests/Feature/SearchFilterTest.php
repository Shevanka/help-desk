<?php

namespace Tests\Feature;

use App\Enums\TicketStatus;
use App\Models\Category;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SearchFilterTest extends TestCase
{
    use RefreshDatabase;

    public function test_keyword_search_matches_subject_number_and_description(): void
    {
        $admin = User::factory()->admin()->create();
        $wifi = Ticket::factory()->create(['subject' => 'Wifi lantai dua mati', 'description' => 'Tidak ada sinyal sama sekali']);
        $printer = Ticket::factory()->create(['subject' => 'Printer macet', 'description' => 'Kertas tersangkut di dalam']);

        $this->actingAs($admin)->get('/tickets?q=wifi')->assertSee('Wifi lantai dua mati')->assertDontSee('Printer macet');
        $this->actingAs($admin)->get('/tickets?q=tersangkut')->assertSee('Printer macet')->assertDontSee('Wifi lantai dua mati');
        $this->actingAs($admin)->get('/tickets?q='.$wifi->ticket_number)->assertSee('Wifi lantai dua mati')->assertDontSee('Printer macet');
    }

    public function test_search_does_not_leak_other_users_tickets(): void
    {
        $owner = User::factory()->requester()->create();
        $stranger = User::factory()->requester()->create();
        Ticket::factory()->for($owner, 'requester')->create(['subject' => 'Rahasia milik owner']);

        $this->actingAs($stranger)->get('/tickets?q=Rahasia')->assertOk()->assertDontSee('Rahasia milik owner');
    }

    public function test_filter_by_category_priority_status_and_date(): void
    {
        $admin = User::factory()->admin()->create();
        $network = Category::factory()->create(['name' => 'Network']);
        $hardware = Category::factory()->create(['name' => 'Hardware']);

        Ticket::factory()->for($network)->create(['subject' => 'Net high open', 'priority' => 'high']);
        Ticket::factory()->for($network)->status(TicketStatus::Resolved)->create(['subject' => 'Net low resolved', 'priority' => 'low']);
        Ticket::factory()->for($hardware)->create(['subject' => 'Hw high open', 'priority' => 'high']);
        $old = Ticket::factory()->for($network)->create(['subject' => 'Net old ticket', 'priority' => 'high']);
        $old->forceFill(['created_at' => now()->subDays(40)])->save();

        $this->actingAs($admin)->get("/tickets?category={$network->id}&priority=high")
            ->assertSee('Net high open')->assertSee('Net old ticket')
            ->assertDontSee('Hw high open')->assertDontSee('Net low resolved');

        $this->actingAs($admin)->get("/tickets?category={$network->id}&status=resolved")
            ->assertSee('Net low resolved')->assertDontSee('Net high open');

        $this->actingAs($admin)->get('/tickets?from='.now()->subDays(7)->toDateString())
            ->assertSee('Net high open')->assertDontSee('Net old ticket');
    }

    public function test_invalid_filters_are_rejected(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->get('/tickets?status=bogus')->assertSessionHasErrors('status');
        $this->actingAs($admin)->get('/tickets?from=2026-05-10&to=2026-05-01')->assertSessionHasErrors('to');
    }

    public function test_technician_filter_is_ignored_for_non_admins(): void
    {
        $tech = User::factory()->technician()->create();
        $other = User::factory()->technician()->create();
        Ticket::factory()->assignedTo($tech)->create(['subject' => 'Mine to fix']);
        Ticket::factory()->assignedTo($other)->create(['subject' => 'Not mine']);

        // Trying to peek at another technician's queue still only returns own tickets.
        $this->actingAs($tech)->get("/tickets?technician={$other->id}")
            ->assertOk()->assertSee('Mine to fix')->assertDontSee('Not mine');
    }

    public function test_admin_can_filter_unassigned_and_per_technician(): void
    {
        $admin = User::factory()->admin()->create();
        $tech = User::factory()->technician()->create();
        Ticket::factory()->assignedTo($tech)->create(['subject' => 'Assigned one']);
        Ticket::factory()->create(['subject' => 'Nobody yet']);

        $this->actingAs($admin)->get('/tickets?technician=none')->assertSee('Nobody yet')->assertDontSee('Assigned one');
        $this->actingAs($admin)->get("/tickets?technician={$tech->id}")->assertSee('Assigned one')->assertDontSee('Nobody yet');
    }

    public function test_ticket_history_shows_only_finished_tickets_of_the_technician(): void
    {
        $tech = User::factory()->technician()->create();
        $other = User::factory()->technician()->create();
        Ticket::factory()->assignedTo($tech)->status(TicketStatus::Resolved)->create(['subject' => 'Done resolved']);
        Ticket::factory()->assignedTo($tech)->status(TicketStatus::Closed)->create(['subject' => 'Done closed']);
        Ticket::factory()->assignedTo($tech)->status(TicketStatus::InProgress)->create(['subject' => 'Still working']);
        Ticket::factory()->assignedTo($other)->status(TicketStatus::Closed)->create(['subject' => 'Someone else closed']);

        $this->actingAs($tech)->get(route('tickets.history'))->assertOk()
            ->assertSee('Done resolved')->assertSee('Done closed')
            ->assertDontSee('Still working')->assertDontSee('Someone else closed');

        // Status tab inside history cannot widen the scope to active tickets.
        $this->actingAs($tech)->get(route('tickets.history', ['status' => 'in_progress']))
            ->assertDontSee('Still working');
    }

    public function test_ticket_history_is_for_technicians_only(): void
    {
        $this->actingAs(User::factory()->requester()->create())->get(route('tickets.history'))->assertForbidden();
        $this->actingAs(User::factory()->admin()->create())->get(route('tickets.history'))->assertForbidden();
    }
}
