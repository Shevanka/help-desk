<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\Category;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_always_creates_requester_even_if_role_is_posted(): void
    {
        $this->post('/register', [
            'name' => 'Sneaky',
            'email' => 'sneaky@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'admin',
        ]);

        $user = User::where('email', 'sneaky@example.com')->firstOrFail();
        $this->assertSame(Role::Requester, $user->role);
        $this->assertNotSame('password', $user->password);
        $this->assertTrue(Hash::check('password', $user->password));
    }

    public function test_profile_update_cannot_change_role(): void
    {
        $user = User::factory()->requester()->create();

        $this->actingAs($user)->patch('/profile', ['name' => 'X', 'email' => $user->email, 'role' => 'admin']);

        $this->assertSame(Role::Requester, $user->fresh()->role);
    }

    public function test_admin_cannot_change_own_role(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->put(route('admin.users.update', $admin), ['name' => $admin->name, 'email' => $admin->email, 'role' => 'requester'])
            ->assertSessionHasErrors('role');

        $this->assertSame(Role::Admin, $admin->fresh()->role);
    }

    public function test_admin_can_promote_user_to_technician(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->requester()->create();

        $this->actingAs($admin)
            ->put(route('admin.users.update', $user), ['name' => $user->name, 'email' => $user->email, 'role' => 'technician'])
            ->assertRedirect(route('admin.users.index'));

        $this->assertSame(Role::Technician, $user->fresh()->role);
    }

    public function test_admin_can_create_technician_account(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->post(route('admin.users.store'), [
            'name' => 'New Tech',
            'email' => 'newtech@example.com',
            'role' => 'technician',
            'password' => 'secret-password',
            'password_confirmation' => 'secret-password',
        ])->assertRedirect(route('admin.users.index'));

        $this->assertSame(Role::Technician, User::where('email', 'newtech@example.com')->first()->role);
    }

    public function test_admin_can_manage_categories(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->post(route('admin.categories.store'), ['name' => 'Printer', 'description' => 'Printer issues', 'is_active' => '1'])
            ->assertRedirect(route('admin.categories.index'));
        $category = Category::where('name', 'Printer')->firstOrFail();
        $this->assertTrue($category->is_active);

        $this->actingAs($admin)->put(route('admin.categories.update', $category), ['name' => 'Printers'])
            ->assertRedirect(route('admin.categories.index'));
        $this->assertFalse($category->fresh()->is_active); // unchecked checkbox → inactive
        $this->assertSame('Printers', $category->fresh()->name);

        $this->actingAs($admin)->delete(route('admin.categories.destroy', $category));
        $this->assertModelMissing($category);
    }

    public function test_category_in_use_is_deactivated_instead_of_deleted(): void
    {
        $admin = User::factory()->admin()->create();
        $category = Category::factory()->create();
        Ticket::factory()->for($category)->create();

        $this->actingAs($admin)->delete(route('admin.categories.destroy', $category));

        $this->assertModelExists($category);
        $this->assertFalse($category->fresh()->is_active);
    }

    public function test_category_name_must_be_unique(): void
    {
        $admin = User::factory()->admin()->create();
        Category::factory()->create(['name' => 'Network']);

        $this->actingAs($admin)->post(route('admin.categories.store'), ['name' => 'Network'])->assertSessionHasErrors('name');
    }
}
