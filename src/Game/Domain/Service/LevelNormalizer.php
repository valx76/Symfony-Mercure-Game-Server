<?php

namespace App\Game\Domain\Service;

use App\Game\Domain\Model\Entity\Level\LevelInterface;
use App\Game\Domain\Model\Entity\World;

final readonly class LevelNormalizer implements LevelNormalizerInterface
{
    public function normalize(World $world, LevelInterface $level): array
    {
        return [
            'level_name' => $level::class,
            'width' => $level->getSize()->x,
            'height' => $level->getSize()->y,
            'tiles' => $level->getTiles(),
            'players' => $this->normalizePlayersOfLevel($world, $level),
        ];
    }

    /**
     * @return array<int, array<string, int|string>>
     */
    private function normalizePlayersOfLevel(World $world, LevelInterface $level): array
    {
        $formattedPlayerData = [];

        foreach ($world->getPlayers() as $player) {
            if ($player->levelName !== $level::class) {
                continue;
            }

            $formattedPlayerData[] = [
                'id' => $player->id,
                'name' => $player->name,
                'x' => $player->position->x,
                'y' => $player->position->y,
            ];
        }

        return $formattedPlayerData;
    }
}
