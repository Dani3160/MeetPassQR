<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CheckInRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'token' => ['required', 'string'],
            'eventId' => ['required', 'integer'],
        ];
    }

    protected function prepareForValidation(): void
    {
        // Get token and eventId from query parameters (for QR code scanning)
        if ($this->has('token') && $this->has('eventId') && $this->has('guestId')) {
            $this->merge([
                'token' => $this->query('token'),
                'eventId' => (int) $this->query('eventId'),
                'guestId' => (int) $this->query('guestId'),
            ]);
        }
    }
}
