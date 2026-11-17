<?php

namespace Tests\Feature;

use App\Enums\TicketStatus;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_and_assigned_technician_can_comment(): void
    {
        $tech = User::factory()->technician()->create();
        $owner = User::factory()->requester()->create();
        $ticket = Ticket::factory()->for($owner, 'requester')->assignedTo($tech)->create();

        $this->actingAs($owner)->post(route('tickets.comments.store', $ticket), ['message' => 'Masih belum bisa.'])->assertSessionHasNoErrors();
        $this->actingAs($tech)->post(route('tickets.comments.store', $ticket), ['message' => 'Sedang saya cek.'])->assertSessionHasNoErrors();

        $this->assertSame(2, $ticket->comments()->count());
        $this->actingAs($owner)->get(route('tickets.show', $ticket))->assertSee('Sedang saya cek.')->assertSee($tech->name);
    }

    public function test_unrelated_users_cannot_comment(): void
    {
        $ticket = Ticket::factory()->create();
        $stranger = User::factory()->requester()->create();
        $otherTech = User::factory()->technician()->create();

        $this->actingAs($stranger)->post(route('tickets.comments.store', $ticket), ['message' => 'hi'])->assertForbidden();
        $this->actingAs($otherTech)->post(route('tickets.comments.store', $ticket), ['message' => 'hi'])->assertForbidden();
        $this->assertDatabaseCount('comments', 0);
    }

    public function test_comment_must_have_content(): void
    {
        $owner = User::factory()->requester()->create();
        $ticket = Ticket::factory()->for($owner, 'requester')->create();

        $this->actingAs($owner)->post(route('tickets.comments.store', $ticket), ['message' => ''])->assertSessionHasErrors('message');
        $this->assertDatabaseCount('comments', 0);
    }

    public function test_closed_ticket_cannot_receive_comments(): void
    {
        $owner = User::factory()->requester()->create();
        $ticket = Ticket::factory()->for($owner, 'requester')->status(TicketStatus::Closed)->create();

        $this->actingAs($owner)->post(route('tickets.comments.store', $ticket), ['message' => 'halo'])->assertForbidden();
    }
}
