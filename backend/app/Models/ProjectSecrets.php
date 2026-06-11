<?php

namespace App\Models;

use App\Enum\GitAuthType;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property GitAuthType $auth_type
 * @property-read \App\Models\Project|null $project
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectSecrets newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectSecrets newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectSecrets query()
 * @mixin \Eloquent
 */
class ProjectSecrets extends Model
{
     use HasUuids;


    protected $primaryKey = 'project_id';
    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'project_id',
        'auth_type',
        'access_token'
    ];

    public function casts(): array
    {
        return [
            'auth_type' => GitAuthType::class,
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
