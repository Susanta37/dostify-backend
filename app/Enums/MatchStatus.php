<?php

namespace App\Enums;

enum MatchStatus: string
{
    case Pending = 'pending';
    case Matched = 'matched';
    case Accepted = 'accepted';
    case Rejected = 'rejected';
    case Expired = 'expired';
    case Cancelled = 'cancelled';
}
