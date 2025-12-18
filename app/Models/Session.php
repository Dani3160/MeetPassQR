<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Session extends Model
{
    protected $table = 'event_sessions';
    
    protected $primaryKey = 'id_session';
    
    public $incrementing = true;
    
    protected $fillable = [
        'id_event',
        'name_session',
        'time_started_session',
        'time_ended_session',
    ];

    protected $casts = [
        'time_started_session' => 'string',
        'time_ended_session' => 'string',
    ];

    /**
     * Get the event that owns the session
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class, 'id_event', 'id_event');
    }

    /**
     * Get all guests for this session
     */
    public function guests(): HasMany
    {
        return $this->hasMany(Guest::class, 'id_session', 'id_session');
    }
}
