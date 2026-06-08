<?php

namespace App\Models;

use App\Enum\ProjectPermissions;
use App\Enum\RolePermissions;
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
use Illuminate\Database\Eloquent\Builder;

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
        'image_url',
        'git_url',
        'default_branch',
    ];

    public function secrets(): HasOne
    {
        return $this->hasOne(ProjectSecrets::class, 'project_id', 'id');
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    public function members(): HasMany
    {
        return $this->hasMany(ProjectMember::class, 'project_id');
    }

    public function scopeVisibleTo(Builder $query, ?User $user): void
{
    $query->where(function ($q) use ($user) {
        if ($user) {
            $userId = $user->id;

            $q->whereHas('group', function ($g) use ($userId) {
                // A. Group Owner has full visibility
                $g->where('owner_id', $userId);
            })
            ->orWhereHas('group.members', function ($gm) use ($userId) {
                // B. Org Admins (PROJECT_MANAGE) have full visibility
                $gm->where('user_id', $userId)
                   ->whereHas('roles', function ($r) {
                       $r->whereJsonContains('permissions', RolePermissions::PROJECT_MANAGE->value);
                   });
            })
            ->orWhereHas('members', function ($m) use ($userId) {
                // C. Explicit Project Members with VIEW permission
                $m->where('user_id', $userId)
                   ->whereJsonContains('permissions', ProjectPermissions::READ->value);
            });
        }
    });
}

    protected static function booted(): void
    {
        static::creating(function (Project $project) {
            if (empty($project->image_url)) {
            // 1. Scan the 'projects' directory inside your 'defaults' disk root
            // (Points to storage/app/assets/default/projects)
            $files = Storage::disk('defaults')->files('projects');

            if (!empty($files)) {
                // 2. Pick a random default image path (e.g. "projects/default1.webp")
                $randomFile = $files[array_rand($files)];
                
                // 3. Store the unified public path relative to /assets
                // Result: "default/projects/default1.webp"
                $project->image_url = 'default/' . $randomFile;
            }
        }
        });
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
        $this->update(['image_url' => '/images' . $path]);

        return $path;
    }
}
