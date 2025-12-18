<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventCertificate extends Model
{
    protected $primaryKey = 'id_certificate';
    
    public $incrementing = true;
    
    protected $fillable = [
        'id_event',
        'id_template',
        'introductory_phrase',
        'completion_phrase',
        'organization_name',
        'signatory_name',
        'signatory_title',
        'signature_image',
        'verification_url_base',
        'certificate_id_prefix',
        'auto_generate_id',
        'custom_fields'
    ];

    protected $casts = [
        'auto_generate_id' => 'boolean',
        'custom_fields' => 'array'
    ];

    /**
     * Get the event that owns this certificate
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class, 'id_event', 'id_event');
    }

    /**
     * Get the template used for this certificate
     */
    public function template(): BelongsTo
    {
        return $this->belongsTo(CertificateTemplate::class, 'id_template', 'id_template');
    }
}
