<?php

namespace App\Enums;

enum CallStatus: string
{
    case Initiated = 'initiated';
    case Ringing = 'ringing';
    case Accepted = 'accepted';
    case Active = 'active';
    case Ended = 'ended';
    case Missed = 'missed';
    case Rejected = 'rejected';
    case Failed = 'failed';
}
