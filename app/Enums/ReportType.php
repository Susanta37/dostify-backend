<?php

namespace App\Enums;

enum ReportType: string
{
    case User = 'user';
    case Room = 'room';
    case Abuse = 'abuse';
    case Message = 'message';
}
