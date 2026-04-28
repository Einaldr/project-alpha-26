<?php

namespace App\Models;

use App\Models\Pivots\GroupMemberRolePivot;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @property string $id
 * @property string $user_id
 * @property string $group_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Group|null $group
 * @property-read GroupMemberRolePivot|null $pivot
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\GroupRole> $roles
 * @property-read int|null $roles_count
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GroupMember newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GroupMember newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GroupMember query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GroupMember whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GroupMember whereGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GroupMember whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GroupMember whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GroupMember whereUserId($value)
 * @mixin \Eloquent
 */
class GroupMember extends Pivot
{
    /** @use HasFactory<\Database\Factories\GroupMemberFactory>
     * HasUUids: Autmatically generates UUIDs for 'id' field.
     */
    use HasFactory, HasUuids;

    protected $table = 'group_members';
    protected $keyType = 'string';
    public $incrementing = false;

    /**
     * The attributes that are mass assignable
     * 
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'group_id',
    ];

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(GroupRole::class, 'pivot_group_roles', 'group_member_id', 'role_id')
                    ->using(GroupMemberRolePivot::class)
                    ->withTimestamps();
    }
}
