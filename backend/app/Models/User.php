<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Enum\AccountStatus;
use App\Enum\GroupType;
use App\Traits\HasGroupPermissions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> 
     * HasUuids: Automatically generates UUIDs for 'id' column.
     * SoftDeletes: Enables standard Laravel soft delete functionallity.
    */
    use HasFactory, Notifiable, HasUuids, SoftDeletes, HasApiTokens, HasGroupPermissions;

    protected $keyType = 'string';
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'tos_accepted_at',
        'tos_version',
        'privacy_policy_accepted_at',
        'privacy_policy_version',
        'account_status'
    ];

    /**
     * The default attributes.
     * 
     * @var array
     */
    protected $attributes = [
        'account_status' => AccountStatus::ACTIVE,
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime:d-m-Y',
            'tos_accepted_at' => 'datetime:d-m-Y',
            'privacy_policy_accepted_at' => 'datetime:d-m-Y',
            'password' => 'hashed',
            'account_status' => AccountStatus::class,
        ];
    }

    // Personal group of the user
    public function personalGroup(): HasOne
    {
        return $this->hasOne(Group::class, 'owner_id')->where('type', GroupType::INDIVIDUAL);
    }

    // All groups owned by the user
    public function ownedGroups(): HasMany
    {
        return $this->hasMany(Group::class, 'owner_id');
    }

    // All groups the user is in
    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(Group::class, 'group_members')->using(GroupMember::class)->withPivot('id')->withTimestamps();
    }

    // All memberships the user has
    public function memberships(): HasMany
    {
        return $this->hasMany(GroupMember::class, 'user_id');
    }

    public function groupLogs(): MorphMany
    {
        return $this->morphMany(AuditLog::class, 'target');
    }
}
