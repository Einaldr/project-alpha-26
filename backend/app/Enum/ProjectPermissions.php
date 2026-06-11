<?php

namespace App\Enum;

enum ProjectPermissions: string
{
    case READ = 'project.read';

    case WRITE = 'project.write';

    case MANAGE = 'project.manage';

    case INVITE = 'project.members.invite';

    case KICK = 'project.members.kick';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
