<?php

namespace App\Models;

use App\Enum\GroupType;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

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
        ];
    }

    public function members(): HasMany
    {
        return $this->hasMany(GroupMember::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'group_members')
                    ->using(GroupMember::class)
                    ->withPivot('id')
                    ->withTimestamps();
    }

    public function auditLogs(): MorphMany
    {
        return $this->morphMany(AuditLog::class, 'target');
    }

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
}
