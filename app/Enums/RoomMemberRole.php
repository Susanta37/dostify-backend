<?php

namespace App\Enums;

enum RoomMemberRole: string
{
    case Host = 'host';
    case Speaker = 'speaker';
    case Listener = 'listener';
}
