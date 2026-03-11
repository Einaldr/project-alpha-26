<?php

namespace App\Enum;

enum AccountStatus: string
{
    case ACTIVE = 'active';
    case DELETED = 'deleted';
    case DEACTIVATED = 'deactivated';
    case BANNED = 'banned';


    public function label(): string
    {
        return match($this) {
            self::ACTIVE => 'Active',
            self::DELETED => 'Deleted',
            self::DEACTIVATED => 'Deactivated',
            self::BANNED => 'Banned',
        };
    }
}
