<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\FileExtension;
use Intervention\Image\ImageManager;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Intervention\Image\Drivers\Gd\Driver;

/**
 * @property-read \App\Models\Group|null $group
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ProjectMember> $members
 * @property-read int|null $members_count
 * @property-read \App\Models\ProjectSecrets|null $secrets
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project withoutTrashed()
 * @mixin \Eloquent
 */
class Project extends Model
{
    use HasUuids, SoftDeletes;

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'group_id',
        'name',
        'description',
        'icon_url',
        'git_url',
        'default_branch',
    ];

    public function secrets(): HasOne
    {
        return $this->hasOne(ProjectSecrets::class, 'id', 'id');
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class, 'id', 'group_id');
    }

    public function members(): HasMany
    {
        return $this->hasMany(ProjectMember::class, 'project_id');
    }

    /**
     * Save user-provided background image.
     * @param UploadedFile $file The user-provided icon.
     * @return string Path to the icon.
     */
    public function saveCustomBackground(UploadedFile $file): string
    {
        $image = new ImageManager(new Driver())->decode($file)->cover(400, 400);

        $path = "groups/{$this->group->id}/{$this->id}/background.webp";
        
        $encoded = $image->encodeUsingFileExtension(FileExtension::WEBP);

        Storage::disk('images')->put($path, $encoded);
        $this->update(['icon_path' => $path]);

        return $path;
    }
}
