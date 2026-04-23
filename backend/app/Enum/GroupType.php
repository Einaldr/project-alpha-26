<?php

namespace App\Enum;

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
