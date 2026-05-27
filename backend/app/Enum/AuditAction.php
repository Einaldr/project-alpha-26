<?php

namespace App\Enum;

/**
 * AuditAction
 * 
 * Defines the strict list of system and user events recorded 
 * inside the group_audit_logs table.
 * 
 * @package App\Enums
 */
enum AuditAction: string
{
    // --- Group & Hierarchy Actions ---
    /** The root Organization or Individual workspace was initialized. */
    case GROUP_CREATED = 'group.created';

    /** The group settings, description, or icons were modified. */
    case GROUP_UPDATED = 'group.updated';

    /** The group (Org or Team) was soft-deleted or purged. */
    case GROUP_DELETED = 'group.deleted';

    /** A sub-team was created under this Organization. */
    case CHILD_CREATED = 'child.created';

    // --- Member Actions ---
    /** A user was invited to the group via email. */
    case MEMBER_INVITED = 'member.invited';

    /** A user accepted their invite and successfully joined. */
    case MEMBER_JOINED = 'member.joined';

    /** A member voluntarily left the group. */
    case MEMBER_LEFT = 'member.left';

    /** A member was removed/kicked from the group. */
    case MEMBER_KICKED = 'member.kicked';

    case MEMBER_UPDATED = 'member.updated';

    // --- Role & Permission Actions ---
    /** A custom role was created within the group. */
    case ROLE_CREATED = 'role.created';

    /** A custom role's permissions or name were updated. */
    case ROLE_UPDATED = 'role.updated';

    /** A custom role was deleted from the group. */
    case ROLE_DELETED = 'role.deleted';

    /**
     * Get a list of all backed string values.
     * 
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
