<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Guest extends Model
{
    protected $primaryKey = 'id_guest';
    
    public $incrementing = true;
    
    protected $fillable = [
        'id_event',
        'id_session',
        'guest_title',
        'guest_name',
        'guest_address',
        'guest_email',
        'guest_phone',
        'guest_label',
        'guest_status',
        'guest_time_arrival',
        'guest_time_leave',
        'guest_pic',
    ];

    protected $casts = [
        'guest_status' => 'boolean',
        'guest_label' => 'integer',
        'guest_time_arrival' => 'datetime',
        'guest_time_leave' => 'datetime',
        'guest_created_at' => 'datetime',
    ];

    /**
     * Get the event that owns the guest
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class, 'id_event', 'id_event');
    }

    /**
     * Get the session that owns the guest
     */
    public function session(): BelongsTo
    {
        return $this->belongsTo(Session::class, 'id_session', 'id_session');
    }

    /**
     * Check if guest has attended
     */
    public function hasAttended(): bool
    {
        return $this->guest_status === true;
    }

    /**
     * Get all custom field values for this guest
     */
    public function customFieldValues()
    {
        return $this->hasMany(\App\Models\GuestCustomFieldValue::class, 'id_guest', 'id_guest');
    }

    /**
     * Get custom field value by field name
     */
    public function getCustomFieldValue(string $fieldName)
    {
        $field = \App\Models\EventCustomField::where('id_event', $this->id_event)
            ->where('field_name', $fieldName)
            ->first();
        
        if (!$field) {
            return null;
        }

        $value = $this->customFieldValues()
            ->where('id_field', $field->id_field)
            ->first();

        return $value ? $value->getDisplayValue() : null;
    }
}
