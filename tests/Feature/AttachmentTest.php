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

class AttachmentTest extends TestCase
{
    use RefreshDatabase;

    private User $requester;
    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');
        $this->requester = User::factory()->requester()->create();
        $this->category = Category::factory()->create();
    }

    private function payload(array $extra = []): array
    {
        return [
            'subject' => 'Layar biru di PC lab',
            'description' => 'Muncul blue screen setiap kali dinyalakan.',
            'category_id' => $this->category->id,
            'priority' => 'medium',
        ] + $extra;
    }

    public function test_requester_can_upload_image_attachment(): void
    {
        $this->actingAs($this->requester)
            ->post(route('tickets.store'), $this->payload(['attachment' => UploadedFile::fake()->image('bluescreen.png', 800, 600)]))
            ->assertSessionHasNoErrors();

        $ticket = Ticket::firstOrFail();
        $this->assertSame('bluescreen.png', $ticket->attachment_name);
        $this->assertStringStartsWith('attachments/', $ticket->attachment);
        $this->assertStringNotContainsString('bluescreen', $ticket->attachment); // stored under a random name
        Storage::disk('local')->assertExists($ticket->attachment);
        $this->assertDatabaseHas('ticket_activities', ['ticket_id' => $ticket->id, 'action' => 'attachment']);
    }

    public function test_pdf_document_is_accepted(): void
    {
        $this->actingAs($this->requester)
            ->post(route('tickets.store'), $this->payload(['attachment' => UploadedFile::fake()->create('error-log.pdf', 200, 'application/pdf')]))
            ->assertSessionHasNoErrors();

        Storage::disk('local')->assertExists(Ticket::firstOrFail()->attachment);
    }

    public function test_disallowed_extension_is_rejected(): void
    {
        $this->actingAs($this->requester)
            ->post(route('tickets.store'), $this->payload(['attachment' => UploadedFile::fake()->create('shell.php', 10, 'application/x-php')]))
            ->assertSessionHasErrors('attachment');

        $this->assertDatabaseCount('tickets', 0);
    }

    public function test_file_whose_content_does_not_match_extension_is_rejected(): void
    {
        // Named .jpg but the content type is an executable.
        $this->actingAs($this->requester)
            ->post(route('tickets.store'), $this->payload(['attachment' => UploadedFile::fake()->create('photo.jpg', 10, 'application/x-msdownload')]))
            ->assertSessionHasErrors('attachment');

        $this->assertDatabaseCount('tickets', 0);
        $this->assertSame([], Storage::disk('local')->allFiles());
    }

    public function test_file_larger_than_5mb_is_rejected(): void
    {
        $this->actingAs($this->requester)
            ->post(route('tickets.store'), $this->payload(['attachment' => UploadedFile::fake()->create('big.pdf', 6000, 'application/pdf')]))
            ->assertSessionHasErrors('attachment');
    }

    public function test_attachment_is_only_served_to_authorised_users(): void
    {
        $tech = User::factory()->technician()->create();
        $admin = User::factory()->admin()->create();
        $stranger = User::factory()->requester()->create();

        $this->actingAs($this->requester)->post(route('tickets.store'), $this->payload([
            'attachment' => UploadedFile::fake()->image('foto.jpg'),
        ]));
        $ticket = Ticket::firstOrFail();
        $ticket->update(['technician_id' => $tech->id]);
        $url = route('tickets.attachment', $ticket);

        $this->actingAs($this->requester)->get($url)->assertOk()
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader('Content-Disposition', 'inline; filename=foto.jpg');
        $this->actingAs($tech)->get($url)->assertOk();
        $this->actingAs($admin)->get($url.'?download=1')->assertOk()->assertDownload('foto.jpg');

        $this->actingAs($stranger)->get($url)->assertForbidden();
        auth()->logout();
        $this->get($url)->assertRedirect(route('login'));
    }

    public function test_documents_are_always_downloaded_not_rendered(): void
    {
        $this->actingAs($this->requester)->post(route('tickets.store'), $this->payload([
            'attachment' => UploadedFile::fake()->create('catatan.txt', 1, 'text/plain'),
        ]));

        $this->actingAs($this->requester)->get(route('tickets.attachment', Ticket::firstOrFail()))->assertDownload('catatan.txt');
    }

    public function test_attachment_files_are_not_publicly_reachable(): void
    {
        $this->actingAs($this->requester)->post(route('tickets.store'), $this->payload([
            'attachment' => UploadedFile::fake()->image('foto.png'),
        ]));
        $path = Ticket::firstOrFail()->attachment;

        $this->get('/storage/'.$path)->assertNotFound();
        $this->get('/'.$path)->assertNotFound();
    }

    public function test_owner_can_replace_and_remove_attachment_while_open(): void
    {
        $this->actingAs($this->requester)->post(route('tickets.store'), $this->payload([
            'attachment' => UploadedFile::fake()->image('first.png'),
        ]));
        $ticket = Ticket::firstOrFail();
        $first = $ticket->attachment;

        $this->actingAs($this->requester)->put(route('tickets.update', $ticket), $this->payload([
            'attachment' => UploadedFile::fake()->image('second.png'),
        ]))->assertSessionHasNoErrors();

        $ticket->refresh();
        $this->assertSame('second.png', $ticket->attachment_name);
        Storage::disk('local')->assertMissing($first);
        Storage::disk('local')->assertExists($ticket->attachment);

        $this->actingAs($this->requester)->put(route('tickets.update', $ticket), $this->payload(['remove_attachment' => '1']));

        $second = $ticket->attachment;
        $ticket->refresh();
        $this->assertNull($ticket->attachment);
        $this->assertNull($ticket->attachment_name);
        Storage::disk('local')->assertMissing($second);
    }

    public function test_owner_cannot_change_attachment_after_ticket_is_in_progress(): void
    {
        $ticket = Ticket::factory()->for($this->requester, 'requester')->status(TicketStatus::InProgress)->create();

        $this->actingAs($this->requester)->put(route('tickets.update', $ticket), $this->payload([
            'attachment' => UploadedFile::fake()->image('late.png'),
        ]))->assertForbidden();

        $this->assertSame([], Storage::disk('local')->allFiles());
    }

    public function test_deleting_ticket_removes_its_file(): void
    {
        $admin = User::factory()->admin()->create();
        $this->actingAs($this->requester)->post(route('tickets.store'), $this->payload([
            'attachment' => UploadedFile::fake()->image('bye.png'),
        ]));
        $ticket = Ticket::firstOrFail();

        $this->actingAs($admin)->delete(route('tickets.destroy', $ticket))->assertRedirect(route('tickets.index'));

        Storage::disk('local')->assertMissing($ticket->attachment);
    }
}
