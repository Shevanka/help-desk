<?php

namespace Tests\Feature;

use App\Enums\TicketStatus;
use App\Models\Category;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_requester_can_create_ticket_which_starts_open(): void
    {
        $requester = User::factory()->requester()->create();
        $category = Category::factory()->create(['name' => 'Network']);

        $response = $this->actingAs($requester)->post(route('tickets.store'), [
            'subject' => 'Internet Connection Unavailable',
            'description' => 'Tidak ada koneksi internet di Lab 1 sejak pagi.',
            'category_id' => $category->id,
            'priority' => 'high',
        ]);

        $ticket = Ticket::firstOrFail();
        $response->assertRedirect(route('tickets.show', $ticket));

        $this->assertSame(TicketStatus::Open, $ticket->status);
        $this->assertSame($requester->id, $ticket->user_id);
        $this->assertMatchesRegularExpression('/^TCK-\d{8}-\d{5}$/', $ticket->ticket_number);
        $this->assertDatabaseHas('ticket_activities', ['ticket_id' => $ticket->id, 'action' => 'created']);
    }

    public function test_ticket_requires_subject_description_category_and_priority(): void
    {
        $requester = User::factory()->requester()->create();

        $this->actingAs($requester)
            ->post(route('tickets.store'), [])
            ->assertSessionHasErrors(['subject', 'description', 'category_id', 'priority']);

        $this->assertDatabaseCount('tickets', 0);
    }

    public function test_inactive_category_cannot_be_used(): void
    {
        $requester = User::factory()->requester()->create();
        $category = Category::factory()->inactive()->create();

        $this->actingAs($requester)->post(route('tickets.store'), [
            'subject' => 'Some subject',
            'description' => 'Some long enough description',
            'category_id' => $category->id,
            'priority' => 'low',
        ])->assertSessionHasErrors('category_id');
    }

    public function test_full_workflow_open_to_closed(): void
    {
        $admin = User::factory()->admin()->create();
        $tech = User::factory()->technician()->create();
        $requester = User::factory()->requester()->create();
        $ticket = Ticket::factory()->for($requester, 'requester')->create();

        // Admin assigns
        $this->actingAs($admin)->patch(route('tickets.assign', $ticket), ['technician_id' => $tech->id])->assertRedirect();
        $this->assertSame($tech->id, $ticket->fresh()->technician_id);
        $this->assertDatabaseHas('ticket_activities', ['ticket_id' => $ticket->id, 'action' => 'assigned', 'new_value' => $tech->name]);

        // Technician starts & resolves
        $this->actingAs($tech)->patch(route('tickets.status', $ticket), ['status' => 'in_progress'])->assertSessionHasNoErrors();
        $this->assertSame(TicketStatus::InProgress, $ticket->fresh()->status);

        $this->actingAs($tech)->patch(route('tickets.status', $ticket), ['status' => 'resolved'])->assertSessionHasNoErrors();
        $this->assertSame(TicketStatus::Resolved, $ticket->fresh()->status);
        $this->assertNotNull($ticket->fresh()->resolved_at);

        // Requester closes
        $this->actingAs($requester)->patch(route('tickets.status', $ticket), ['status' => 'closed'])->assertSessionHasNoErrors();
        $this->assertSame(TicketStatus::Closed, $ticket->fresh()->status);
        $this->assertNotNull($ticket->fresh()->closed_at);

        $this->assertSame(3, $ticket->activities()->where('action', 'status_changed')->count());
    }

    public function test_requester_cannot_move_ticket_to_in_progress(): void
    {
        $requester = User::factory()->requester()->create();
        $ticket = Ticket::factory()->for($requester, 'requester')->create();

        $this->actingAs($requester)
            ->patch(route('tickets.status', $ticket), ['status' => 'in_progress'])
            ->assertForbidden();

        $this->assertSame(TicketStatus::Open, $ticket->fresh()->status);
    }

    public function test_technician_cannot_skip_workflow_steps(): void
    {
        $tech = User::factory()->technician()->create();
        $ticket = Ticket::factory()->assignedTo($tech)->create();

        $this->actingAs($tech)
            ->patch(route('tickets.status', $ticket), ['status' => 'closed'])
            ->assertSessionHasErrors('status');

        $this->assertSame(TicketStatus::Open, $ticket->fresh()->status);
    }

    public function test_technician_cannot_touch_unassigned_ticket(): void
    {
        $tech = User::factory()->technician()->create();
        $ticket = Ticket::factory()->create();

        $this->actingAs($tech)->patch(route('tickets.status', $ticket), ['status' => 'in_progress'])->assertForbidden();
        $this->actingAs($tech)->patch(route('tickets.priority', $ticket), ['priority' => 'critical'])->assertForbidden();
    }

    public function test_only_admin_can_assign(): void
    {
        $tech = User::factory()->technician()->create();
        $requester = User::factory()->requester()->create();
        $ticket = Ticket::factory()->for($requester, 'requester')->assignedTo($tech)->create();

        $this->actingAs($tech)->patch(route('tickets.assign', $ticket), ['technician_id' => $tech->id])->assertForbidden();
        $this->actingAs($requester)->patch(route('tickets.assign', $ticket), ['technician_id' => $tech->id])->assertForbidden();
    }

    public function test_admin_cannot_assign_ticket_to_non_technician(): void
    {
        $admin = User::factory()->admin()->create();
        $requester = User::factory()->requester()->create();
        $ticket = Ticket::factory()->create();

        $this->actingAs($admin)
            ->patch(route('tickets.assign', $ticket), ['technician_id' => $requester->id])
            ->assertSessionHasErrors('technician_id');

        $this->assertNull($ticket->fresh()->technician_id);
    }

    public function test_assigned_technician_can_change_priority_and_it_is_logged(): void
    {
        $tech = User::factory()->technician()->create();
        $ticket = Ticket::factory()->assignedTo($tech)->create(['priority' => 'low']);

        $this->actingAs($tech)->patch(route('tickets.priority', $ticket), ['priority' => 'critical'])->assertSessionHasNoErrors();

        $this->assertSame('critical', $ticket->fresh()->priority->value);
        $this->assertDatabaseHas('ticket_activities', ['ticket_id' => $ticket->id, 'action' => 'priority_changed', 'old_value' => 'Low', 'new_value' => 'Critical']);
    }

    public function test_requester_can_edit_own_ticket_only_while_open(): void
    {
        $requester = User::factory()->requester()->create();
        $ticket = Ticket::factory()->for($requester, 'requester')->create();
        $payload = ['subject' => 'Updated subject', 'description' => 'Updated description text', 'category_id' => $ticket->category_id];

        $this->actingAs($requester)->put(route('tickets.update', $ticket), $payload)->assertRedirect(route('tickets.show', $ticket));
        $this->assertSame('Updated subject', $ticket->fresh()->subject);

        $ticket->update(['status' => TicketStatus::InProgress]);
        $this->actingAs($requester)->put(route('tickets.update', $ticket), $payload)->assertForbidden();
    }
}
