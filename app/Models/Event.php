<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Event extends Model
{
    protected $primaryKey = 'id_event';
    
    public $incrementing = true;
    
    protected $fillable = [
        'id_user',
        'name_event',
        'slug',
        'date_event',
        'location_event',
        'guest_total',
        'event_default_guest_pic',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-generate slug when creating
        static::creating(function ($event) {
            if (empty($event->slug)) {
                $event->slug = static::generateUniqueSlug($event->name_event);
            }
        });

        // Auto-update slug when name changes
        static::updating(function ($event) {
            if ($event->isDirty('name_event')) {
                // Always regenerate slug when name changes
                $event->slug = static::generateUniqueSlug($event->name_event, $event->id_event);
            }
        });
    }

    /**
     * Generate unique slug from name
     */
    protected static function generateUniqueSlug(string $name, ?int $excludeId = null): string
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $counter = 1;

        while (static::where('slug', $slug)
            ->when($excludeId, function ($query) use ($excludeId) {
                return $query->where('id_event', '!=', $excludeId);
            })
            ->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    protected $casts = [
        'date_event' => 'date',
        'guest_total' => 'integer',
        'event_created_at' => 'datetime',
    ];

    /**
     * Get the user that owns the event
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    /**
     * Get all guests for this event
     */
    public function guests(): HasMany
    {
        return $this->hasMany(Guest::class, 'id_event', 'id_event');
    }

    /**
     * Get all sessions for this event
     */
    public function sessions(): HasMany
    {
        return $this->hasMany(Session::class, 'id_event', 'id_event');
    }

    /**
     * Get all custom fields for this event
     */
    public function customFields(): HasMany
    {
        return $this->hasMany(EventCustomField::class, 'id_event', 'id_event')
            ->orderBy('field_order');
    }

    /**
     * Get the certificate configuration for this event
     */
    public function certificate(): HasMany
    {
        return $this->hasMany(EventCertificate::class, 'id_event', 'id_event');
    }
}
