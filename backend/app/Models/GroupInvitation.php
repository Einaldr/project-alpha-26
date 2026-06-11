<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property-read \App\Models\Group|null $group
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GroupInvitation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GroupInvitation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GroupInvitation query()
 * @property string $id
 * @property string $group_id
 * @property string $email
 * @property string $token
 * @property string $expires_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GroupInvitation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GroupInvitation whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GroupInvitation whereExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GroupInvitation whereGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GroupInvitation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GroupInvitation whereToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GroupInvitation whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class GroupInvitation extends Model
{
    use HasUuids;

    protected $fillable = [
        'group_id',
        'email',
        'role_ids',
    ];

    protected function casts(): array
    {
        return [
            'role_ids' => 'array',
        ];
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }
}
