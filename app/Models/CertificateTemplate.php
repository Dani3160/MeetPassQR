<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CertificateTemplate extends Model
{
    protected $primaryKey = 'id_template';
    
    public $incrementing = true;
    
    protected $fillable = [
        'template_name',
        'template_description',
        'html_structure',
        'css_styles',
        'preview_image',
        'configurable_fields',
        'is_active'
    ];

    protected $casts = [
        'configurable_fields' => 'array',
        'is_active' => 'boolean'
    ];

    /**
     * Get all event certificates using this template
     */
    public function eventCertificates(): HasMany
    {
        return $this->hasMany(EventCertificate::class, 'id_template', 'id_template');
    }
}
