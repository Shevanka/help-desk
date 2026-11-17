<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /** Demo accounts – every password is "password". */
    public const ACCOUNTS = [
        ['Administrator', 'admin@helpdesk.test', Role::Admin],
        ['Budi Teknisi', 'tech1@helpdesk.test', Role::Technician],
        ['Sari Teknisi', 'tech2@helpdesk.test', Role::Technician],
        ['Andi Mahasiswa', 'student1@helpdesk.test', Role::Requester],
        ['Rina Mahasiswa', 'student2@helpdesk.test', Role::Requester],
    ];

    public function run(): void
    {
        $password = Hash::make('password');

        foreach (self::ACCOUNTS as [$name, $email, $role]) {
            $user = User::firstOrNew(['email' => $email]);
            $user->fill(['name' => $name, 'password' => $password]);
            $user->role = $role;
            $user->email_verified_at = now();
            $user->save();
        }
    }
}
