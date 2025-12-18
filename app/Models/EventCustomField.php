<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EventCustomField extends Model
{
    protected $primaryKey = 'id_field';
    
    protected $fillable = [
        'id_event',
        'field_name',
        'field_label',
        'field_type',
        'field_options',
        'is_required',
        'field_order',
        'field_placeholder',
        'field_validation',
    ];

    protected $casts = [
        'field_options' => 'array',
        'field_validation' => 'array',
        'is_required' => 'boolean',
        'field_order' => 'integer',
    ];

    /**
     * Get the event that owns the custom field
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class, 'id_event', 'id_event');
    }

    /**
     * Get all values for this field
     */
    public function values(): HasMany
    {
        return $this->hasMany(GuestCustomFieldValue::class, 'id_field', 'id_field');
    }

    /**
     * Get field options as array
     */
    public function getOptionsArray(): array
    {
        if (!$this->field_options || !isset($this->field_options['options'])) {
            return [];
        }
        
        return $this->field_options['options'];
    }

    /**
     * Check if field type supports options
     */
    public function supportsOptions(): bool
    {
        return in_array($this->field_type, ['select', 'radio', 'checkbox']);
    }

    /**
     * Validate field value
     */
    public function validateValue($value): bool
    {
        // Required validation
        if ($this->is_required && empty($value)) {
            return false;
        }

        // Type-specific validation
        switch ($this->field_type) {
            case 'select':
            case 'radio':
                $options = $this->getOptionsArray();
                $validValues = array_column($options, 'value');
                return in_array($value, $validValues);
            
            case 'checkbox':
                if (!is_array($value)) {
                    return false;
                }
                $options = $this->getOptionsArray();
                $validValues = array_column($options, 'value');
                return empty(array_diff($value, $validValues));
            
            case 'file':
                // File validation should be done in controller
                return true;
            
            default:
                return true;
        }
    }
}
