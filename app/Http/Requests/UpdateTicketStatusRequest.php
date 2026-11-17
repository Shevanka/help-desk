<?php

namespace App\Http\Requests;

use App\Enums\TicketStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class UpdateTicketStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('changeStatus', $this->route('ticket'));
    }

    public function rules(): array
    {
        $allowed = array_map(
            fn (TicketStatus $s) => $s->value,
            Gate::getPolicyFor($this->route('ticket'))->allowedStatuses($this->user(), $this->route('ticket')),
        );

        return [
            'status' => ['required', Rule::in($allowed)],
            'note' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'status.in' => 'Perubahan status ini tidak diizinkan untuk Anda.',
        ];
    }
}
