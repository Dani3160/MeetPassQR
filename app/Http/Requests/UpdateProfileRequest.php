<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'guestName' => ['required', 'string', 'max:100'],
            'guestPhone' => ['nullable', 'string', 'max:20'],
            'guestEmail' => ['nullable', 'email', 'max:100'],
        ];
    }

    protected function prepareForValidation(): void
    {
        // Get token, eventId, and guestId from query parameters
        if ($this->has('token') && $this->has('eventId') && $this->has('guestId')) {
            $this->merge([
                'token' => $this->query('token'),
                'eventId' => (int) $this->query('eventId'),
                'guestId' => (int) $this->query('guestId'),
            ]);
        }
    }
}
