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
