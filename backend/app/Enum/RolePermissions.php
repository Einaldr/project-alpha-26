<?php

namespace App\Enum;

enum RolePermissions: string
{
    // Group permissions
    case GROUP_VIEW = 'group.view';
    case GROUP_UPDATE = 'group.update';
    case GROUP_DELETE = 'group.delete';
    case GROUP_CREATE_CHILD = 'group.create_child';
    case GROUP_VIEW_CHILD = 'group.view_child';
    
    // Member permissions
    case MEMBER_INVITE = 'member.invite';
    case MEMBER_KICK = 'member.kick';
    case MEMBER_MANAGE_ROLES = 'members.manage_roles';

    // Roles permissions
    case ROLES_MANAGE = 'roles.manage';

    // Project permissions
    case PROJECT_MANAGE = 'project.manage';
    case PROJECT_INVITE = 'project.invite';
    case PROJECT_KICK = 'project.kick';

    // Repository permissions
    case REPOSITORY_MANAGE = 'repository.manage';

    // Audit log
    case AUDIT_LOG_VIEW = 'audit_log.view';

    public static function value(): array
    {
        return array_column(self::cases(), 'value');
    }
}
