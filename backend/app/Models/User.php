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

/**
 * @property string $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property \Illuminate\Support\Carbon|null $tos_accepted_at
 * @property string $tos_version
 * @property string|null $privacy_policy_acknowledged_at
 * @property string $privacy_policy_version
 * @property AccountStatus $account_status
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AuditLog> $groupLogs
 * @property-read int|null $group_logs_count
 * @property-read \App\Models\GroupMember|null $pivot
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Group> $groups
 * @property-read int|null $groups_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\GroupMember> $memberships
 * @property-read int|null $memberships_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Group> $ownedGroups
 * @property-read int|null $owned_groups_count
 * @property-read \App\Models\Group|null $personalGroup
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereAccountStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePrivacyPolicyAcknowledgedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePrivacyPolicyVersion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTosAcceptedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTosVersion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutTrashed()
 * @mixin \Eloquent
 */
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
