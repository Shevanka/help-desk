<?php

namespace App\Http\Requests;

use App\Enums\TicketPriority;
use App\Http\Requests\Concerns\ValidatesAttachment;
use App\Models\Ticket;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTicketRequest extends FormRequest
{
    use ValidatesAttachment;

    public function authorize(): bool
    {
        return $this->user()->can('create', Ticket::class);
    }

    public function rules(): array
    {
        return [
            'subject' => ['required', 'string', 'min:5', 'max:255'],
            'description' => ['required', 'string', 'min:10', 'max:5000'],
            'category_id' => ['required', 'integer', Rule::exists('categories', 'id')->where('is_active', true)],
            'priority' => ['required', Rule::enum(TicketPriority::class)],
            'attachment' => $this->attachmentRules(),
        ];
    }

    public function messages(): array
    {
        return $this->attachmentMessages();
    }

    public function attributes(): array
    {
        return [
            'subject' => 'subject',
            'description' => 'deskripsi',
            'category_id' => 'kategori',
            'priority' => 'prioritas',
            'attachment' => 'lampiran',
        ];
    }
}
