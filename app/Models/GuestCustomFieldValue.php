<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GuestCustomFieldValue extends Model
{
    protected $primaryKey = 'id_value';
    
    protected $fillable = [
        'id_guest',
        'id_field',
        'field_value',
        'file_path',
    ];

    protected $casts = [
        'field_value' => 'string',
    ];

    /**
     * Get the guest that owns this value
     */
    public function guest(): BelongsTo
    {
        return $this->belongsTo(Guest::class, 'id_guest', 'id_guest');
    }

    /**
     * Get the custom field definition
     */
    public function customField(): BelongsTo
    {
        return $this->belongsTo(EventCustomField::class, 'id_field', 'id_field');
    }

    /**
     * Get display value based on field type
     */
    public function getDisplayValue(): string
    {
        $field = $this->customField;
        
        if (!$field) {
            return $this->field_value ?? '';
        }

        switch ($field->field_type) {
            case 'select':
            case 'radio':
                $options = $field->getOptionsArray();
                foreach ($options as $option) {
                    if ($option['value'] === $this->field_value) {
                        return $option['label'];
                    }
                }
                return $this->field_value ?? '';
            
            case 'checkbox':
                if (is_string($this->field_value)) {
                    $values = json_decode($this->field_value, true) ?? [];
                } else {
                    $values = $this->field_value ?? [];
                }
                
                $options = $field->getOptionsArray();
                $labels = [];
                foreach ($options as $option) {
                    if (in_array($option['value'], $values)) {
                        $labels[] = $option['label'];
                    }
                }
                return implode(', ', $labels);
            
            case 'file':
                return $this->file_path ? basename($this->file_path) : '';
            
            default:
                return $this->field_value ?? '';
        }
    }
}
