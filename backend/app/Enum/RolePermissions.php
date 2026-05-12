<?php

namespace App\Enum;

/**
 * RolePermissions
 * 
 * Defines granular permission strings used across the RBAC system.
 * These values are stored in the 'permissions' JSONB column.
 */
enum RolePermissions: string
{
    // --- Group Management ---
    // Can view the group.
    case GROUP_VIEW = 'group.view';
    // Can modify group settings, icons, and billing info.
    case GROUP_UPDATE = 'group.update';
    // Can delete the group (usually reserved for group owners).
    case GROUP_DELETE = 'group.delete';
    // Can create child teams (if the group is an ORG).
    case GROUP_CREATE_CHILD = 'group.create_child';
    // Can see private child teams.
    case GROUP_VIEW_CHILD = 'group.view_child';
    
    // --- Member Management ---
    // Can generate invite links and add users to the group.
    case MEMBER_INVITE = 'member.invite';
    // Can remove members from the group (excluding the owner).
    case MEMBER_KICK = 'member.kick';
    // Can add and delete member roles.
    case MEMBER_MANAGE_ROLES = 'members.manage_roles';

    // --- Roles Management ---
    // Can create, modify and delete group roles.
    case ROLES_MANAGE = 'roles.manage';

    // --- Project Management ---
    // Can create, modify and delete projects within the group.
    case PROJECT_MANAGE = 'project.manage';
    // Can generate invite links and add users to a project.
    case PROJECT_INVITE = 'project.invite';
    // Can kick users from a project.
    case PROJECT_KICK = 'project.kick';

    // --- Repository Management ---
    // Can manage the repository.
    case REPOSITORY_MANAGE = 'repository.manage';

    // --- Audit Log Management ---
    // Can see the group's audit log.
    case AUDIT_LOG_VIEW = 'audit_log.view';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
