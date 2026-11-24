<?php

namespace Tests\Feature;

use App\Enums\TicketStatus;
use App\Models\Ticket;
use App\Models\User;
use App\Notifications\TicketAssigned;
use App\Notifications\TicketCommented;
use App\Notifications\TicketStatusChanged;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use RuntimeException;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_requester_is_emailed_when_technician_changes_status(): void
    {
        Notification::fake();
        $requester = User::factory()->requester()->create();
        $tech = User::factory()->technician()->create();
        $ticket = Ticket::factory()->for($requester, 'requester')->assignedTo($tech)->create();

        $this->actingAs($tech)
            ->patch(route('tickets.status', $ticket), ['status' => 'in_progress', 'note' => 'Sedang dicek ke lab'])
            ->assertSessionHasNoErrors();

        Notification::assertSentTo($requester, TicketStatusChanged::class, function (TicketStatusChanged $n) use ($ticket, $tech) {
            return $n->ticket->is($ticket)
                && $n->oldStatus === TicketStatus::Open
                && $n->newStatus === TicketStatus::InProgress
                && $n->actor->is($tech)
                && $n->note === 'Sedang dicek ke lab';
        });
        Notification::assertNotSentTo($tech, TicketStatusChanged::class);
    }

    public function test_requester_is_not_emailed_about_their_own_status_change(): void
    {
        Notification::fake();
        $requester = User::factory()->requester()->create();
        $tech = User::factory()->technician()->create();
        $ticket = Ticket::factory()->for($requester, 'requester')->assignedTo($tech)
            ->status(TicketStatus::Resolved)->create();

        $this->actingAs($requester)
            ->patch(route('tickets.status', $ticket), ['status' => 'closed'])
            ->assertSessionHasNoErrors();

        Notification::assertNothingSent();
    }

    public function test_technician_is_emailed_when_assigned(): void
    {
        Notification::fake();
        $admin = User::factory()->admin()->create();
        $tech = User::factory()->technician()->create();
        $ticket = Ticket::factory()->create();

        $this->actingAs($admin)
            ->patch(route('tickets.assign', $ticket), ['technician_id' => $tech->id])
            ->assertRedirect();

        Notification::assertSentTo($tech, TicketAssigned::class, fn (TicketAssigned $n) => $n->ticket->is($ticket));
        Notification::assertCount(1);
    }

    public function test_unassigning_sends_no_email(): void
    {
        $admin = User::factory()->admin()->create();
        $tech = User::factory()->technician()->create();
        $ticket = Ticket::factory()->assignedTo($tech)->create();
        Notification::fake();

        $this->actingAs($admin)
            ->patch(route('tickets.assign', $ticket), ['technician_id' => null])
            ->assertRedirect();

        Notification::assertNothingSent();
    }

    public function test_comment_notifies_other_participants_only(): void
    {
        Notification::fake();
        $requester = User::factory()->requester()->create();
        $tech = User::factory()->technician()->create();
        $ticket = Ticket::factory()->for($requester, 'requester')->assignedTo($tech)->create();

        $this->actingAs($requester)
            ->post(route('tickets.comments.store', $ticket), ['message' => 'Masih belum bisa.'])
            ->assertSessionHasNoErrors();

        Notification::assertSentTo($tech, TicketCommented::class,
            fn (TicketCommented $n) => $n->comment->message === 'Masih belum bisa.');
        Notification::assertNotSentTo($requester, TicketCommented::class);
    }

    public function test_comment_on_unassigned_ticket_by_admin_notifies_requester(): void
    {
        Notification::fake();
        $admin = User::factory()->admin()->create();
        $requester = User::factory()->requester()->create();
        $ticket = Ticket::factory()->for($requester, 'requester')->create();

        $this->actingAs($admin)
            ->post(route('tickets.comments.store', $ticket), ['message' => 'Mohon kirim foto errornya.'])
            ->assertSessionHasNoErrors();

        Notification::assertSentTo($requester, TicketCommented::class);
        Notification::assertCount(1);
    }

    public function test_status_email_content(): void
    {
        $requester = User::factory()->requester()->create(['name' => 'Budi']);
        $tech = User::factory()->technician()->create(['name' => 'Teknisi A']);
        $ticket = Ticket::factory()->for($requester, 'requester')->assignedTo($tech)->create(['subject' => 'Printer macet']);

        $mail = (new TicketStatusChanged($ticket, TicketStatus::InProgress, TicketStatus::Resolved, $tech, 'Roller diganti'))
            ->toMail($requester);

        $this->assertSame("[{$ticket->ticket_number}] Status berubah menjadi Resolved", $mail->subject);
        $this->assertSame('Halo, Budi', $mail->greeting);
        $this->assertSame(route('tickets.show', $ticket), $mail->actionUrl);

        $body = implode("\n", $mail->introLines);
        $this->assertStringContainsString('Printer macet', $body);
        $this->assertStringContainsString('**In Progress** menjadi **Resolved** oleh Teknisi A', $body);
        $this->assertStringContainsString('Roller diganti', $body);
    }

    public function test_mail_failure_does_not_break_the_workflow(): void
    {
        $requester = User::factory()->requester()->create();
        $tech = User::factory()->technician()->create();
        $ticket = Ticket::factory()->for($requester, 'requester')->assignedTo($tech)->create();

        Notification::shouldReceive('send')->once()->andThrow(new RuntimeException('SMTP down'));

        $this->actingAs($tech)
            ->patch(route('tickets.status', $ticket), ['status' => 'in_progress'])
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $this->assertSame(TicketStatus::InProgress, $ticket->fresh()->status);
    }
}
