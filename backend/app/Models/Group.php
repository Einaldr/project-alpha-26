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

    // Scope by role
    public function scopeVisibleTo($query, $userId = null)
    {
        return $query->where(function ($q) use ($userId) {
            $q->where('is_private_child', false);

            if ($userId) {
                $q->orWhereHas('users', function ($sq) use ($userId) {
                    $sq->where('users.id', $userId);
                });
            }
        });
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
     * Utility
     * =======================
     */

    // Generate custom icon
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
}
