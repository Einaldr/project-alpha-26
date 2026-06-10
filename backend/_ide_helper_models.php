<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * @property-read \App\Models\Group|null $group
 * @property-read Model|\Eloquent $target
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog withoutTrashed()
 * @mixin \Eloquent
 * @property string $id
 * @property string $group_id
 * @property string|null $user_id
 * @property \App\Enum\AuditAction $action
 * @property string|null $target_type
 * @property string|null $target_id
 * @property array<array-key, mixed>|null $payload
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereAction($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog wherePayload($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereTargetId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereTargetType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereUserId($value)
 */
	class AuditLog extends \Eloquent {}
}

namespace App\Models{
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
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\GroupRole> $groupRoles
 * @property-read int|null $group_roles_count
 */
	class Group extends \Eloquent {}
}

namespace App\Models{
/**
 * @property-read \App\Models\Group|null $group
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GroupInvitation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GroupInvitation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GroupInvitation query()
 * @mixin \Eloquent
 * @property string $id
 * @property string $group_id
 * @property string $email
 * @property string $token
 * @property string $expires_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GroupInvitation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GroupInvitation whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GroupInvitation whereExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GroupInvitation whereGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GroupInvitation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GroupInvitation whereToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GroupInvitation whereUpdatedAt($value)
 */
	class GroupInvitation extends \Eloquent {}
}

namespace App\Models{
/**
 * @property string $id
 * @property string $user_id
 * @property string $group_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Group|null $group
 * @property-read GroupMemberRolePivot|null $pivot
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\GroupRole> $roles
 * @property-read int|null $roles_count
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GroupMember newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GroupMember newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GroupMember query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GroupMember whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GroupMember whereGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GroupMember whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GroupMember whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GroupMember whereUserId($value)
 * @mixin \Eloquent
 * @method static \Database\Factories\GroupMemberFactory factory($count = null, $state = [])
 */
	class GroupMember extends \Eloquent {}
}

namespace App\Models{
/**
 * @property string $id
 * @property string $group_id
 * @property string $name
 * @property array<array-key, mixed> $permissions
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\GroupMember> $members
 * @property-read int|null $members_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GroupRole newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GroupRole newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GroupRole query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GroupRole whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GroupRole whereGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GroupRole whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GroupRole whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GroupRole wherePermissions($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GroupRole whereUpdatedAt($value)
 * @property-read \App\Models\Group|null $group
 * @mixin \Eloquent
 */
	class GroupRole extends \Eloquent {}
}

namespace App\Models\Pivots{
/**
 * @property string $id
 * @property string $group_member_id
 * @property string $role_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GroupMemberRolePivot newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GroupMemberRolePivot newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GroupMemberRolePivot query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GroupMemberRolePivot whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GroupMemberRolePivot whereGroupMemberId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GroupMemberRolePivot whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GroupMemberRolePivot whereRoleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GroupMemberRolePivot whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	class GroupMemberRolePivot extends \Eloquent {}
}

namespace App\Models{
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
 */
	class Project extends \Eloquent {}
}

namespace App\Models{
/**
 * @property-read \App\Models\Project|null $project
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectMember newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectMember newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectMember query()
 */
	class ProjectMember extends \Eloquent {}
}

namespace App\Models{
/**
 * @property \App\Enum\GitAuthType $auth_type
 * @property-read \App\Models\Project|null $project
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectSecrets newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectSecrets newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectSecrets query()
 */
	class ProjectSecrets extends \Eloquent {}
}

namespace App\Models{
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
	class User extends \Eloquent {}
}

