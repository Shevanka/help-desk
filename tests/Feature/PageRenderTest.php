<?php

namespace Tests\Feature;

use App\Enums\TicketStatus;
use App\Models\Category;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Smoke test: every page each role can reach renders without a server error.
 */
class PageRenderTest extends TestCase
{
    use RefreshDatabase;

    public function test_all_pages_render_for_every_role(): void
    {
        Storage::fake('local');
        $admin = User::factory()->admin()->create();
        $tech = User::factory()->technician()->create();
        $requester = User::factory()->requester()->create();
        $category = Category::factory()->create();

        // A ticket with an image attachment, comments and history.
        $this->actingAs($requester)->post(route('tickets.store'), [
            'subject' => 'Smoke test ticket',
            'description' => 'Dibuat untuk menguji semua halaman.',
            'category_id' => $category->id,
            'priority' => 'high',
            'attachment' => UploadedFile::fake()->image('bukti.png'),
        ])->assertSessionHasNoErrors();
        $ticket = Ticket::firstOrFail();
        $this->actingAs($admin)->patch(route('tickets.assign', $ticket), ['technician_id' => $tech->id]);
        $this->actingAs($tech)->post(route('tickets.comments.store', $ticket), ['message' => 'Dicek.']);
        Ticket::factory()->assignedTo($tech)->status(TicketStatus::Closed)->create();

        $pages = [
            [$requester, ['/dashboard', '/tickets', '/tickets/create', "/tickets/{$ticket->ticket_number}", "/tickets/{$ticket->ticket_number}/edit", '/profile']],
            [$tech, ['/dashboard', '/tickets', '/tickets/history', "/tickets/{$ticket->ticket_number}", '/profile']],
            [$admin, ['/dashboard', '/tickets', '/tickets/create', "/tickets/{$ticket->ticket_number}", "/tickets/{$ticket->ticket_number}/edit",
                '/admin/users', '/admin/users/create', "/admin/users/{$tech->id}/edit",
                '/admin/categories', '/admin/categories/create', "/admin/categories/{$category->id}/edit",
                '/admin/reports', '/admin/reports?from=2025-01-01&to='.now()->toDateString(), '/profile']],
        ];

        foreach ($pages as [$user, $urls]) {
            foreach ($urls as $url) {
                $this->actingAs($user)->get($url)->assertOk();
            }
        }
    }
}
