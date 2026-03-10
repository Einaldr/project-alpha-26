<?php

namespace App;

enum AccountStatus
{
    case Deactivated;
    case Deleted;
    case Banned;
    case Active;
}
