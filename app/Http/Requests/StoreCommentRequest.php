<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCommentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('comment', $this->route('ticket'));
    }

    public function rules(): array
    {
        return [
            'message' => ['required', 'string', 'max:3000'],
        ];
    }

    public function attributes(): array
    {
        return ['message' => 'komentar'];
    }
}
