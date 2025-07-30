<?php

namespace App\Game\Domain\Model\Entity;

enum PlayerNotificationTypeEnum: string
{
    case EXCEPTION = 'EXCEPTION';
    case DISCONNECT = 'DISCONNECT';
    case LEVEL_CHANGE = 'LEVEL_CHANGE';
}
