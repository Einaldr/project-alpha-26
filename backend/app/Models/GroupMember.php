<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\Pivot;

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
        return $this->belongsToMany(GroupRole::class, 'pivot_group_roles', 'group_member_id', 'role_id')->withTimestamps();
    }
}
