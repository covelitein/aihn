<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubscriptionApplication extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'plan_id',
        'transaction_id',
        'amount_paid',
        'payment_method',
        'proof_of_payment',
        'status',
        'admin_notes',
        'rejection_reason',
        'submitted_at',
        'reviewed_at',
        'approved_at',
        'expires_at'
    ];

    protected $casts = [
        'amount_paid' => 'decimal:2',
        'submitted_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'approved_at' => 'datetime',
        'expires_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function plan()
    {
        return $this->belongsTo(SubscriptionPlan::class, 'plan_id');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeUnderReview($query)
    {
        return $query->where('status', 'under_review');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'approved')
                    ->where('expires_at', '>', now());
    }

    public function scopeExpired($query)
    {
        return $query->where(function($q) {
            $q->where('status', 'expired')
              ->orWhere(function($q2) {
                  $q2->where('status', 'approved')
                     ->where('expires_at', '<=', now());
              });
        });
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('submitted_at', '>=', now()->subDays($days));
    }

    // Accessors
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'pending' => 'secondary',
            'under_review' => 'warning',
            'approved' => 'success',
            'rejected' => 'danger',
            'expired' => 'dark'
        ];

        return $badges[$this->status] ?? 'secondary';
    }

    public function getIsActiveAttribute()
    {
        return $this->status === 'approved' && $this->expires_at > now();
    }

    public function getDaysUntilExpiryAttribute()
    {
        if (!$this->expires_at || $this->expires_at <= now()) {
            return 0;
        }

        return now()->diffInDays($this->expires_at);
    }

    // Methods
    public function markAsUnderReview()
    {
        $this->update([
            'status' => 'under_review',
            'reviewed_at' => now()
        ]);
    }

    public function approve($notes = null)
    {
        $expiresAt = $this->plan->calculateExpiryDate(now());

        $this->update([
            'status' => 'approved',
            'admin_notes' => $notes,
            'approved_at' => now(),
            'expires_at' => $expiresAt
        ]);

        // Update user subscription
        $this->user->update([
            'is_subscribed' => true,
            'subscription_status' => 'active',
            'subscription_expires_at' => $expiresAt,
            'current_subscription_id' => $this->id,
            'last_subscription_at' => now(),
            'total_subscriptions' => $this->user->total_subscriptions + 1
        ]);
    }

    public function reject($reason = null)
    {
        $this->update([
            'status' => 'rejected',
            'rejection_reason' => $reason,
            'reviewed_at' => now()
        ]);
    }

    public function markAsExpired()
    {
        $this->update(['status' => 'expired']);

        // Update user subscription status if this was their current subscription
        if ($this->user->current_subscription_id === $this->id) {
            $this->user->update([
                'is_subscribed' => false,
                'subscription_status' => 'expired'
            ]);
        }
    }
}