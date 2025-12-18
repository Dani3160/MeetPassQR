<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendee extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'ticket_number',
        'event_name',
        'qr_code',
        'is_validated',
        'validated_at',
    ];

    protected $casts = [
        'is_validated' => 'boolean',
        'validated_at' => 'datetime',
    ];

    /**
     * Generate unique QR code identifier
     */
    public function generateQrCode(): string
    {
        return md5($this->email . $this->ticket_number . time());
    }

    /**
     * Check if attendee is already validated
     */
    public function isValidated(): bool
    {
        return $this->is_validated === true;
    }

    /**
     * Mark attendee as validated
     */
    public function markAsValidated(): void
    {
        $this->update([
            'is_validated' => true,
            'validated_at' => now(),
        ]);
    }
}

