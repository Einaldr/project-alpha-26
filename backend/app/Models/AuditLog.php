<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property-read \App\Models\Group|null $group
 * @property-read Model|\Eloquent $target
 * @property-read \App\Models\User|null $user
 * @method static \Database\Factories\AuditLogFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog withoutTrashed()
 * @mixin \Eloquent
 */
class AuditLog extends Model
{
    /** @use HasFactory<\Database\Factories\AuditLogFactory> */
    use HasFactory, HasUuids, SoftDeletes;

    protected $keyType = 'string';
    public $incrementing = false;

    /**
     * List of attributes that are mass assignable.
     * 
     * @var list<string>
     */
    protected $fillable = [
        'group_id',
        'user_id',
        'action',
        'target_id',
        'target_type',
        'payload',
    ];

    protected $casts = [
        'payload' => 'array',
    ];

    // The target of the action
    public function target(): MorphTo
    {
        return $this->morphTo();
    }

    // The group this log entry belongs to
    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    // The user who preformed the action
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
