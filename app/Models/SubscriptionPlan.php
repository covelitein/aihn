<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubscriptionPlan extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'duration',
        'duration_in_months',
        'features',
        'is_active',
        'sort_order'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'features' => 'array',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Relationships
    public function applications()
    {
        return $this->hasMany(SubscriptionApplication::class, 'plan_id');
    }

    public function activeApplications()
    {
        return $this->hasMany(SubscriptionApplication::class, 'plan_id')
            ->whereIn('status', ['approved', 'under_review']);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('price');
    }

    // Accessors
    public function getFormattedPriceAttribute()
    {
        return '$' . number_format($this->price, 2);
    }

    public function getDurationTextAttribute()
    {
        return ucfirst($this->duration);
    }

    // Helper methods
    public function calculateExpiryDate($startDate = null)
    {
        $startDate = $startDate ?: now();
        return $startDate->addMonths($this->duration_in_months);
    }
}
