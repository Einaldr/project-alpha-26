<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class GroupRole extends Model
{
    /** @use HasFactory<\Database\Factories\GroupRoleFactory>
     * HasUUids: Autmatically generates UUIDs for 'id' field.
    */
    use HasFactory, HasUuids;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        group_id,
        name,
        permissions
    ];

    protected $casts = [
        'permissions' => 'array'
    ];

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(GroupMember::class, 'pivot_group_roles', 'role_id', 'group_member_id')->withTimestamps();
    }
}
