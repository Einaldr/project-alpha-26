<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property string $id
 * @property string $group_id
 * @property string $name
 * @property array<array-key, mixed> $permissions
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\GroupMember> $members
 * @property-read int|null $members_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GroupRole newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GroupRole newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GroupRole query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GroupRole whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GroupRole whereGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GroupRole whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GroupRole whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GroupRole wherePermissions($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GroupRole whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class GroupRole extends Model
{
    /** @use HasFactory<\Database\Factories\GroupRoleFactory>
     * HasUUids: Autmatically generates UUIDs for 'id' field.
    */
    use HasFactory, HasUuids;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'group_id',
        'name',
        'permissions'
    ];
    
    protected function casts(): array 
    {
        return [
            'permissions' => 'array'
        ];
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(GroupMember::class, 'pivot_group_roles', 'role_id', 'group_member_id')->withTimestamps();
    }
}
