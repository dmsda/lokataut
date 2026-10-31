<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailyStat extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'daily_stats';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'aggregate_key',
        'link_id',
        'link_slug',
        'date',
        'impressions',
        'verified',
        'completions',
        'unique_visitors',
        'desktop_count',
        'mobile_count',
        'tablet_count',
        'bot_count',
        'top_countries',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'date' => 'date:Y-m-d',
            'impressions' => 'integer',
            'verified' => 'integer',
            'completions' => 'integer',
            'unique_visitors' => 'integer',
            'desktop_count' => 'integer',
            'mobile_count' => 'integer',
            'tablet_count' => 'integer',
            'bot_count' => 'integer',
            'top_countries' => 'array',
        ];
    }

    /**
     * Associated link (nullable upon hard delete).
     */
    public function link(): BelongsTo
    {
        return $this->belongsTo(Link::class, 'link_id')->withTrashed();
    }
}

