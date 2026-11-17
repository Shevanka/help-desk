<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\ValidatesAttachment;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTicketRequest extends FormRequest
{
    use ValidatesAttachment;

    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('ticket'));
    }

    public function rules(): array
    {
        $ticket = $this->route('ticket');

        return [
            'subject' => ['required', 'string', 'min:5', 'max:255'],
            'description' => ['required', 'string', 'min:10', 'max:5000'],
            // Allow keeping the current category even if it was deactivated later.
            'category_id' => ['required', 'integer', Rule::exists('categories', 'id')->where(
                fn ($q) => $q->where('is_active', true)->orWhere('id', $ticket->category_id)
            )],
            'attachment' => $this->attachmentRules(),
            'remove_attachment' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return $this->attachmentMessages();
    }

    public function attributes(): array
    {
        return [
            'description' => 'deskripsi',
            'category_id' => 'kategori',
            'attachment' => 'lampiran',
        ];
    }
}
