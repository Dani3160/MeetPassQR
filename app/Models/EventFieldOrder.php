<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventFieldOrder extends Model
{
    protected $primaryKey = 'id_order';
    
    public $incrementing = true;
    
    protected $fillable = [
        'id_event',
        'form_type',
        'field_type',
        'field_key',
        'field_order',
        'is_visible',
    ];

    protected $casts = [
        'field_order' => 'integer',
        'is_visible' => 'boolean',
    ];

    /**
     * Get the event that owns this field order
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class, 'id_event', 'id_event');
    }
}
