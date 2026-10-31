<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VisitorSession extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'visitor_sessions';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'link_id',
        'token_hash',
        'user_agent_hash',
        'turnstile_verified',
        'turnstile_verified_at',
        'countdown_started_at',
        'countdown_seconds',
        'completed',
        'completed_at',
        'expires_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'turnstile_verified' => 'boolean',
            'turnstile_verified_at' => 'datetime',
            'countdown_started_at' => 'datetime',
            'countdown_seconds' => 'integer',
            'completed' => 'boolean',
            'completed_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    /**
     * Link associated with this session.
     */
    public function link(): BelongsTo
    {
        return $this->belongsTo(Link::class, 'link_id')->withTrashed();
    }

    /**
     * Visits recorded in this session.
     */
    public function visits(): HasMany
    {
        return $this->hasMany(Visit::class, 'visitor_session_id');
    }

    /**
     * Check if this session is expired.
     */
    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }
}

