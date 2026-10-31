<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Visit extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'visits';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'link_id',
        'link_id_snapshot',
        'link_slug_snapshot',
        'visitor_session_id',
        'event_type',
        'ip_hash',
        'device_type',
        'browser',
        'os',
        'country_code',
        'referrer',
        'created_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'link_id_snapshot' => 'integer',
            'created_at' => 'datetime',
        ];
    }

    /**
     * Associated link (nullable upon hard delete).
     */
    public function link(): BelongsTo
    {
        return $this->belongsTo(Link::class, 'link_id')->withTrashed();
    }

    /**
     * Associated visitor session.
     */
    public function visitorSession(): BelongsTo
    {
        return $this->belongsTo(VisitorSession::class, 'visitor_session_id');
    }
}

