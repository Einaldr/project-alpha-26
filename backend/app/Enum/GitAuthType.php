<?php

namespace App\Enum;

enum GitAuthType: string
{
    case HTTP = 'git.auth.http';
    case NONE = 'none';
}
