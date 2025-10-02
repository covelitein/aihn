<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin',
        'subscription_expires_at',
        'is_subscribed',
        'subscription_status',
        'current_subscription_id',
        'last_subscription_at',
        'total_subscriptions'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'subscription_expires_at' => 'datetime',
            'last_subscription_at' => 'datetime',
            'is_subscribed' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'is_admin' => 'boolean'
        ];
    }

    public function isAdmin(): bool
    {
        return $this->is_admin === true;
    }

    public function subscriptionApplications()
    {
        return $this->hasMany(SubscriptionApplication::class);
    }

    public function currentSubscription()
    {
        return $this->belongsTo(SubscriptionApplication::class, 'current_subscription_id');
    }

    public function profile()
    {
        return $this->hasOne(SubscriberProfile::class);
    }

    public function activeSubscription()
    {
        return $this->hasOne(SubscriptionApplication::class, 'id', 'current_subscription_id')
            ->where('status', 'approved')
            ->where('expires_at', '>', now());
    }

    // Scopes
    public function scopeSubscribed($query)
    {
        return $query->where('is_subscribed', true)
            ->where('subscription_status', 'active')
            ->where('subscription_expires_at', '>', now());
    }

    public function scopeExpired($query)
    {
        return $query->where(function ($q) {
            $q->where('subscription_status', 'expired')
                ->orWhere('subscription_expires_at', '<=', now());
        });
    }

    public function scopePending($query)
    {
        return $query->where('subscription_status', 'pending');
    }

    public function getIsSubscriptionActiveAttribute()
    {
        return $this->is_subscribed &&
            $this->subscription_status === 'active' &&
            $this->subscription_expires_at &&
            $this->subscription_expires_at->isFuture();
    }

    public function getSubscriptionDaysLeftAttribute()
    {
        if (!$this->subscription_expires_at || !$this->is_subscription_active) {
            return 0;
        }

        return now()->diffInDays($this->subscription_expires_at);
    }

    public function canAccessPremiumContent()
    {
        return $this->is_subscription_active;
    }

    public function hasPendingApplication()
    {
        return $this->subscriptionApplications()
            ->whereIn('status', ['pending', 'under_review'])
            ->exists();
    }
}
