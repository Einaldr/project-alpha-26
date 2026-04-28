<?php

namespace App\Enum;

/**
 * GroupType
 * 
 * Defines nature of a Group and its place in hierarchy.
 * 
 * Hierarchy rules:
 *  - INDIVIDUAL: isolated user-bound workspaces. No children allowed.
 *  - ORG: Root level organization. Can contain Teams.
 *  - TEAM: Child level. Must belong to a ORG.
 */
enum GroupType: string
{
    
    case INDIVIDUAL = 'individual';
    case TEAM = 'team';
    case ORG = 'org';

    public function label(): string
    {
        return match($this) {
            self::INDIVIDUAL => 'Individual',
            self::TEAM => 'Team',
            self::ORG => 'Organization'
        };
    }
}
