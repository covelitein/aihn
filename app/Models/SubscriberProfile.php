<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriberProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'company_name',
        'phone',
        'address',
        'city',
        'state',
        'country',
        'postal_code',
        'website',
        'notes',
        'preferences'
    ];

    protected $casts = [
        'preferences' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Accessors
    public function getFullAddressAttribute()
    {
        $parts = array_filter([
            $this->address,
            $this->city,
            $this->state,
            $this->postal_code,
            $this->country
        ]);

        return implode(', ', $parts);
    }

    public function getWebsiteUrlAttribute()
    {
        if (!$this->website) {
            return null;
        }

        if (!preg_match("~^(?:f|ht)tps?://~i", $this->website)) {
            return "http://" . $this->website;
        }

        return $this->website;
    }
}