<?php

namespace App\Game\Domain\Service;

use App\Game\Domain\Exception\EntityHasIncorrectDataException;
use App\Game\Domain\Exception\EntityHasMissingDataException;
use App\Game\Domain\Exception\PlayerNotFoundException;
use App\Game\Domain\Model\Entity\Player;
use App\Game\Domain\Model\Repository\PlayerRepositoryInterface;
use Psr\Clock\ClockInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

final readonly class TimeoutPlayerFinder
{
    public function __construct(
        private PlayerRepositoryInterface $playerRepository,
        private ClockInterface $clock,

        #[Autowire('%env(int:PLAYER_INACTIVITY_TIMEOUT_SECS)%')]
        private int $playerInactivityTimeout,
    ) {
    }

    /**
     * @return Player[]
     *
     * @throws EntityHasIncorrectDataException
     * @throws EntityHasMissingDataException
     * @throws PlayerNotFoundException
     * @throws \DateInvalidOperationException
     * @throws \DateMalformedIntervalStringException
     */
    public function find(): array
    {
        $players = $this->playerRepository->findAll();

        $dateTimeLimit = $this->clock->now()->sub(
            new \DateInterval(
                sprintf('PT%dS', $this->playerInactivityTimeout)
            )
        );

        return array_values(
            array_filter(
                $players,
                fn (Player $player) => $player->lastActivityTime->getTimestamp() <= $dateTimeLimit->getTimestamp()
            )
        );
    }
}
