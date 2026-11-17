<?php

namespace App\Http\Requests\Admin;

use App\Enums\Role;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isAdmin();
    }

    public function rules(): array
    {
        $target = $this->route('user');

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique('users', 'email')->ignore($target)],
            'role' => [
                'required',
                Rule::enum(Role::class),
                // An admin can never change their own role.
                function (string $attribute, mixed $value, \Closure $fail) use ($target) {
                    if ($target->is($this->user()) && $value !== $target->role->value) {
                        $fail('Anda tidak dapat mengubah role akun Anda sendiri.');
                    }
                },
            ],
        ];
    }
}
