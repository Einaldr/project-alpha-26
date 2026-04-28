<?php

namespace App\Models;

use App\Enum\GroupType;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Alignment;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\FileExtension;
use Intervention\Image\ImageManager;
use Intervention\Image\Typography\FontFactory;
use Pest\Plugin\Manager;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @property string $id
 * @property string $name
 * @property string|null $parent_id
 * @property bool $is_private_child
 * @property GroupType $type
 * @property string $owner_id
 * @property string|null $billing_email
 * @property string|null $icon_path
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AuditLog> $auditLogs
 * @property-read int|null $audit_logs_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Group> $children
 * @property-read int|null $children_count
 * @property-read mixed $icon_url
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\GroupMember> $members
 * @property-read int|null $members_count
 * @property-read \App\Models\User|null $owner
 * @property-read Group|null $parent
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\GroupRole> $roles
 * @property-read int|null $roles_count
 * @property-read \App\Models\GroupMember|null $pivot
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @method static \Database\Factories\GroupFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Group memberOf($user)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Group newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Group newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Group onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Group public()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Group query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Group visibleTo($user)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Group whereBillingEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Group whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Group whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Group whereIconPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Group whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Group whereIsPrivateChild($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Group whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Group whereOwnerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Group whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Group whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Group whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Group withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Group withoutTrashed()
 * @mixin \Eloquent
 */
class Group extends Model
{
    /** @use HasFactory<\Database\Factories\GroupFactory>
     * HasUUids: Automatically generates UUIDs for 'id' column.
     * SoftDeletes: Enables standard Laravel soft delete functionality.
    */
    use HasFactory, HasUuids, SoftDeletes;

    protected $keyType = 'string';
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     * 
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'owner_id',
        'parent_id',
        'is_private_child',
        'type',
        'billing_email',
        'icon_path'
    ];

    /**
     * Get the attributes that should be cast.
     * 
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'type' => GroupType::class,
            'is_private_child' => 'boolean',
        ];
    }

    // Get GroupMembers associated with this group
    public function members(): HasMany
    {
        return $this->hasMany(GroupMember::class);
    }

    // Get users associated with this group
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'group_members')
                    ->using(GroupMember::class)
                    ->withPivot('id')
                    ->withTimestamps();
    }
    
    // Get audit logs associated with this group
    public function auditLogs(): MorphMany
    {
        return $this->morphMany(AuditLog::class, 'target');
    }

    // Get all roles associated with this group
    public function roles(): HasMany
    {
        return $this->hasMany(GroupRole::class);
    }

    // Get the groups's parent group
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Group::class, 'parent_id', 'id');
    }

    // Get the group's Owner
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id', 'id');
    }

    // Get group's children
    public function children(): HasMany
    {
        return $this->hasMany(Group::class, 'parent_id', 'id');
    }

    /**
     * =======================
     * ATTRIBUTES
     * =======================
     */

    // Generate Url to the group's icon
    protected function iconUrl(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->icon_path ? asset('assets/icons/' . $this->icon_path) : null,
        );
    }

    /**
     * =======================
     * SCOPES
     * =======================
     */

    // Scope by role
    public function scopeMemberOf($query, $user)
    {
        $userId = $user instanceof User ? $user->id : $user;

        return $query->where(function ($q) use ($user, $userId) {
            $q->where('owner_id', $userId)

              ->orWhereHas('members', function ($m) use ($userId) {
                $m->where('user_id', $userId);
              })
              ->orWhereHas('parent', function ($p) use ($userId) {
                $p->where('owner_id', $userId)
                  ->orWhereHas('members', function ($m) use ($userId) {
                    $m->where('user_id', $userId);
                  });
              });
        });

    }

    public function scopePublic($query)
    {
        $query->where('is_private_child', false)
              ->where('type', '!=', GroupType::INDIVIDUAL);
    }

     public function scopeVisibleTo($query, $user) {
        $query->where(function ($q) use ($user) {
            $q->public();

            if ($user) {
               $q->orWhere(function ($q) use ($user) {
                $q->memberOf($user);
               });
            }
        });
    }

    /**
     * =======================
     * Utility
     * =======================
     */

    /**
     * Generate default icon.
     * 
     * @return string Path to the icon.
     */
    public function generateDefaultIcon(): string
    {
        $colors = ['#5865F2', '#EB459E', '#F47B67', '#FEE75C', '#3BA55C', '#72767D'];
        $index = crc32($this->id) % count($colors);
        $backgroundColor = $colors[$index];
        $initial = strtoupper(mb_substr($this->name, 0, 1));

        $manager = new ImageManager(new Driver());

        $image = $manager->createImage(400, 400)->fill($backgroundColor);

        $image->text($initial, 200, 200, function (FontFactory $font) {
            $font->file(storage_path('fonts/Inter-VariableFont_opsz,wght.ttf'));
            $font->size(200);
            $font->color('#ffffff');
            $font->align(Alignment::CENTER, Alignment::CENTER);
        });

        $path = "groups/{$this->id}/icon.webp";
        $encoded = $image->encodeUsingFileExtension(FileExtension::WEBP);

        Storage::disk('icons')->put($path, $encoded);

        $this->update(['icon_path' => $path]);

        return $path;
    }

    /**
     * Save user-provided icon.
     * @param UploadedFile $file The user-provided icon.
     * @return string Path to the icon.
     */
    public function saveCustomIcon(UploadedFile $file): string
    {
        $image = new ImageManager(new Driver())->decode($file)->cover(400, 400);

        $path = "groups/{$this->id}/icon.webp";
        
        $encoded = $image->encodeUsingFileExtension(FileExtension::WEBP);

        Storage::disk('icons')->put($path, $encoded);
        $this->update(['icon_path' => $path]);

        return $path;
    }
}
