<?php

namespace App\Models;

use App\Enum\AuditAction;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

/**
 * @property-read \App\Models\Group|null $group
 * @property-read Model|\Eloquent $target
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog withoutTrashed()
 * @property string $id
 * @property string $group_id
 * @property string|null $user_id
 * @property AuditAction $action
 * @property string|null $target_type
 * @property string|null $target_id
 * @property array<array-key, mixed>|null $payload
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereAction($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog wherePayload($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereTargetId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereTargetType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereUserId($value)
 * @mixin \Eloquent
 */
class AuditLog extends Model
{
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
        'action' => AuditAction::class
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

     public static function log(
        Group|string $group, 
        AuditAction $action, 
        ?Model $target = null,
        array $payload = []
    ): self {
        return self::create([
            'group_id'    => $group instanceof Group ? $group->id : $group,
            'user_id'     => Auth::id(),
            'action'      => $action,
            
            'target_id'   => $target?->getKey(), 
            'target_type' => $target ? $target->getMorphClass() : null, 
            
            'payload'     => $payload,
        ]);
    }
}
