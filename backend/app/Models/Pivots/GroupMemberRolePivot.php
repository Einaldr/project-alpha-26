<?php

namespace App\Models\Pivots;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @property string $id
 * @property string $group_member_id
 * @property string $role_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GroupMemberRolePivot newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GroupMemberRolePivot newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GroupMemberRolePivot query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GroupMemberRolePivot whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GroupMemberRolePivot whereGroupMemberId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GroupMemberRolePivot whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GroupMemberRolePivot whereRoleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GroupMemberRolePivot whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class GroupMemberRolePivot extends Pivot
{
    use HasUuids;

    protected $table = 'pivot_group_roles';

    public $incrementing = false;

    protected $keyType = 'string';
}
