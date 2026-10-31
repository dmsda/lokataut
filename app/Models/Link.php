<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Link extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'links';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'slug',
        'destination_url',
        'title',
        'source_website',
        'status',
        'countdown_seconds',
        'expires_at',
        'total_visits',
        'total_completions',
        'meta_description',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'destination_url' => 'encrypted',
            'countdown_seconds' => 'integer',
            'total_visits' => 'integer',
            'total_completions' => 'integer',
            'expires_at' => 'datetime',
        ];
    }

    /**
     * Visitor sessions for this link.
     */
    public function visitorSessions(): HasMany
    {
        return $this->hasMany(VisitorSession::class, 'link_id');
    }

    /**
     * Raw visit events for this link.
     */
    public function visits(): HasMany
    {
        return $this->hasMany(Visit::class, 'link_id');
    }

    /**
     * Aggregated daily statistics for this link.
     */
    public function dailyStats(): HasMany
    {
        return $this->hasMany(DailyStat::class, 'link_id');
    }

    /**
     * Scope a query to only include active and non-expired links.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active')
            ->where(function (Builder $subQuery) {
                $subQuery->whereNull('expires_at')
                    ->orWhere('expires_at', '>', Carbon::now());
            });
    }

    /**
     * Check if the link has expired.
     */
    public function isExpired(): bool
    {
        return $this->expires_at !== null && $this->expires_at->isPast();
    }
}

