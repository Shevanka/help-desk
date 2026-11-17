<?php

namespace App\Http\Requests;

use App\Enums\Role;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AssignTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('assign', $this->route('ticket'));
    }

    public function rules(): array
    {
        return [
            'technician_id' => [
                'nullable',
                'integer',
                Rule::exists('users', 'id')->where('role', Role::Technician->value),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'technician_id.exists' => 'Teknisi yang dipilih tidak valid.',
        ];
    }
}
