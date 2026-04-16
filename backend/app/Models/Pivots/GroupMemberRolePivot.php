<?php

namespace App\Models\Pivots;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\Pivot;

class GroupMemberRolePivot extends Pivot
{
    use HasUuids;

    protected $table = 'pivot_group_roles';

    public $incrementing = false;

    protected $keyType = 'string';
}
