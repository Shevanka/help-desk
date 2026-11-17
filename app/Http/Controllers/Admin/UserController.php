<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'role' => ['nullable', Rule::enum(Role::class)],
        ]);

        return view('admin.users.index', [
            'users' => User::query()
                ->withCount(['tickets', 'assignedTickets'])
                ->when($filters['role'] ?? null, fn ($q, $role) => $q->where('role', $role))
                ->orderBy('name')
                ->paginate(15)
                ->withQueryString(),
            'filters' => $filters,
        ]);
    }

    public function create(): View
    {
        return view('admin.users.form', ['user' => new User()]);
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $user = new User($data);   // name, email, password (hashed by cast)
        $user->role = $data['role']; // role is not mass-assignable on purpose
        $user->email_verified_at = now();
        $user->save();

        return redirect()->route('admin.users.index')->with('status', "Akun {$user->name} berhasil dibuat.");
    }

    public function edit(User $user): View
    {
        return view('admin.users.form', ['user' => $user]);
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $data = $request->validated();

        $user->fill(['name' => $data['name'], 'email' => $data['email']]);
        $user->role = $data['role'];
        $user->save();

        return redirect()->route('admin.users.index')->with('status', "Akun {$user->name} berhasil diperbarui.");
    }
}
