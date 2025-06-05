<?php

namespace App\Game\Domain\Model\Entity;

final class World
{
    /** @param Player[] $players */
    public function __construct(
        public private(set) readonly string $id,
        public private(set) readonly string $name,
        private array $players,
    ) {
    }

    /** @return Player[] */
    public function getPlayers(): array
    {
        return $this->players;
    }

    public function getPlayersCount(): int
    {
        return count($this->getPlayers());
    }

    public function addPlayer(Player $player): void
    {
        if ($this->hasPlayer($player)) {
            return;
        }

        $this->players[] = $player;
    }

    public function hasPlayer(Player $player): bool
    {
        return array_any($this->players, fn (Player $worldPlayer) => $worldPlayer->id === $player->id);
    }

    public function removePlayer(Player $player): void
    {
        if (!$this->hasPlayer($player)) {
            return;
        }

        /** @var int|false $playerIndex */
        $playerIndex = array_search($player, $this->players);

        if (false === $playerIndex) {
            return;
        }

        array_splice(
            $this->players,
            $playerIndex,
            1,
        );
    }
}
