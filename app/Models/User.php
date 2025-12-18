<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Model
{
    protected $primaryKey = 'id_user';
    
    public $incrementing = true;
    
    protected $fillable = [
        'email',
        'password',
        'firstname',
        'lastname',
        'token',
    ];

    protected $hidden = [
        'password',
    ];

    // Note: Password is stored as MD5 hash (legacy system)
    // Do not use automatic hashing

    /**
     * Get all events for this user
     */
    public function events(): HasMany
    {
        return $this->hasMany(Event::class, 'id_user', 'id_user');
    }

    /**
     * Get full name
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->firstname} {$this->lastname}";
    }
}
