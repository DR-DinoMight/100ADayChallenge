<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class MagicLink extends Model
{
    protected $fillable = [
        'user_id',
        'email',
        'token',
        'expires_at',
        'used',
        'ip_address',
        'user_agent',
        'last_attempt_at',
        'attempt_count',
        'blocked',
        'blocked_until',
        'block_reason',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'used' => 'boolean',
        'last_attempt_at' => 'datetime',
        'blocked' => 'boolean',
        'blocked_until' => 'datetime',
    ];

    public static function createForUser(User $user, ?string $ipAddress = null, ?string $userAgent = null): self
    {
        return static::create([
            'user_id' => $user->id,
            'email' => $user->email,
            'token' => Str::random(64),
            'expires_at' => now()->addHours(24), // Links expire in 24 hours
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
            'last_attempt_at' => now(),
            'attempt_count' => 1,
        ]);
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function isValid(): bool
    {
        return ! $this->used && $this->expires_at->isFuture();
    }

    public function markAsUsed(): void
    {
        $this->update(['used' => true]);
    }

    public function scopeValid($query)
    {
        return $query->where('used', false)
            ->where('expires_at', '>', now());
    }

    public function scopeNotBlocked($query)
    {
        return $query->where(function ($q) {
            $q->where('blocked', false)
                ->orWhere(function ($subQ) {
                    $subQ->where('blocked', true)
                        ->where('blocked_until', '<', now());
                });
        });
    }

    public function scopeRecent($query, int $minutes = 60)
    {
        return $query->where('created_at', '>=', now()->subMinutes($minutes));
    }

    public function isBlocked(): bool
    {
        return $this->blocked && $this->blocked_until && $this->blocked_until->isFuture();
    }

    public function block(string $reason, int $hours = 24): void
    {
        $this->update([
            'blocked' => true,
            'blocked_until' => now()->addHours($hours),
            'block_reason' => $reason,
        ]);
    }

    public function unblock(): void
    {
        $this->update([
            'blocked' => false,
            'blocked_until' => null,
            'block_reason' => null,
        ]);
    }

    public function incrementAttempts(): void
    {
        $this->increment('attempt_count');
        $this->update(['last_attempt_at' => now()]);
    }

    public static function getRecentAttemptsForEmail(string $email, int $minutes = 60): int
    {
        return static::where('email', $email)
            ->recent($minutes)
            ->count();
    }

    public static function getRecentAttemptsForIp(string $ipAddress, int $minutes = 60): int
    {
        return static::where('ip_address', $ipAddress)
            ->recent($minutes)
            ->count();
    }

    public static function getDailyAttemptsForEmail(string $email): int
    {
        return static::where('email', $email)
            ->whereDate('created_at', today())
            ->count();
    }

    public static function getDailyAttemptsForIp(string $ipAddress): int
    {
        return static::where('ip_address', $ipAddress)
            ->whereDate('created_at', today())
            ->count();
    }

    public static function isEmailBlocked(string $email): bool
    {
        return static::where('email', $email)
            ->where('blocked', true)
            ->where('blocked_until', '>', now())
            ->exists();
    }

    public static function isIpBlocked(string $ipAddress): bool
    {
        return static::where('ip_address', $ipAddress)
            ->where('blocked', true)
            ->where('blocked_until', '>', now())
            ->exists();
    }

    public static function cleanupExpired(): int
    {
        return static::where('expires_at', '<', now())
            ->orWhere(function ($query) {
                $query->where('blocked', true)
                    ->where('blocked_until', '<', now());
            })
            ->delete();
    }
}
