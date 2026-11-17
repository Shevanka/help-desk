<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketAccessTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $tech;
    private User $otherTech;
    private User $owner;
    private User $stranger;
    private Ticket $ticket;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create();
        $this->tech = User::factory()->technician()->create();
        $this->otherTech = User::factory()->technician()->create();
        $this->owner = User::factory()->requester()->create();
        $this->stranger = User::factory()->requester()->create();

        $this->ticket = Ticket::factory()
            ->for($this->owner, 'requester')
            ->for(Category::factory())
            ->assignedTo($this->tech)
            ->create(['subject' => 'Owner ticket subject']);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/dashboard')->assertRedirect(route('login'));
        $this->get('/tickets')->assertRedirect(route('login'));
        $this->get(route('tickets.show', $this->ticket))->assertRedirect(route('login'));
    }

    public function test_each_role_sees_its_own_dashboard(): void
    {
        $this->actingAs($this->admin)->get('/dashboard')->assertOk()->assertSee('Dashboard Administrator');
        $this->actingAs($this->tech)->get('/dashboard')->assertOk()->assertSee('Dashboard Teknisi');
        $this->actingAs($this->owner)->get('/dashboard')->assertOk()->assertSee('Riwayat Laporan Terbaru');
    }

    public function test_ticket_detail_access_is_limited_to_owner_assigned_technician_and_admin(): void
    {
        $url = route('tickets.show', $this->ticket);

        $this->actingAs($this->owner)->get($url)->assertOk();
        $this->actingAs($this->tech)->get($url)->assertOk();
        $this->actingAs($this->admin)->get($url)->assertOk();

        $this->actingAs($this->stranger)->get($url)->assertForbidden();
        $this->actingAs($this->otherTech)->get($url)->assertForbidden();
    }

    public function test_ticket_list_is_scoped_per_role(): void
    {
        $this->actingAs($this->owner)->get('/tickets')->assertOk()->assertSee('Owner ticket subject');
        $this->actingAs($this->stranger)->get('/tickets')->assertOk()->assertDontSee('Owner ticket subject');
        $this->actingAs($this->tech)->get('/tickets')->assertOk()->assertSee('Owner ticket subject');
        $this->actingAs($this->otherTech)->get('/tickets')->assertOk()->assertDontSee('Owner ticket subject');
        $this->actingAs($this->admin)->get('/tickets')->assertOk()->assertSee('Owner ticket subject');
    }

    public function test_admin_pages_are_forbidden_for_non_admins(): void
    {
        foreach ([$this->owner, $this->tech] as $user) {
            $this->actingAs($user)->get('/admin/categories')->assertForbidden();
            $this->actingAs($user)->get('/admin/users')->assertForbidden();
        }

        $this->actingAs($this->admin)->get('/admin/categories')->assertOk();
        $this->actingAs($this->admin)->get('/admin/users')->assertOk();
    }

    public function test_technician_cannot_create_tickets(): void
    {
        $this->actingAs($this->tech)->get(route('tickets.create'))->assertForbidden();
    }

    public function test_requester_cannot_edit_or_delete_someone_elses_ticket(): void
    {
        $this->actingAs($this->stranger)->get(route('tickets.edit', $this->ticket))->assertForbidden();
        $this->actingAs($this->owner)->delete(route('tickets.destroy', $this->ticket))->assertForbidden();
        $this->assertModelExists($this->ticket);
    }
}
