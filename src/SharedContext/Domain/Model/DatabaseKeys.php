<?php

namespace App\SharedContext\Domain\Model;

class DatabaseKeys
{
    public const string PLAYER_KEY = 'player_%s';
    public const string PLAYER_NAME = 'name';
    public const string PLAYER_POSITION = 'position';
    public const string PLAYER_LAST_ACTIVITY_TIME = 'last_activity_time';
    public const string PLAYER_WORLD = 'world';
    public const string PLAYER_LEVEL = 'level';

    public const string WORLD_KEY = 'world_%s';
    public const string WORLD_NAME = 'name';
    public const string WORLD_PLAYERS = 'players';
}
