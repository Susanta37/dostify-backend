<?php

namespace App\Enums;

enum RoomType: string
{
    case Public = 'public';
    case Private = 'private';
    case Scheduled = 'scheduled';
}
