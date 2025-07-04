<?php

namespace App\Game\Infrastructure\Scheduler;

use App\Game\Application\UseCase\DisconnectPlayer\DisconnectPlayerAsyncMessage;
use App\Game\Domain\Exception\EntityHasIncorrectDataException;
use App\Game\Domain\Exception\EntityHasMissingDataException;
use App\Game\Domain\Exception\PlayerNotFoundException;
use App\Game\Domain\Service\TimeoutPlayerFinder;
use App\SharedContext\Application\Bus\MessageBusInterface;
use Symfony\Component\Scheduler\Attribute\AsPeriodicTask;

#[AsPeriodicTask(frequency: '10 second')]
final readonly class DeletePlayerTimeoutSchedule
{
    public function __construct(
        private TimeoutPlayerFinder $timeoutPlayerFinder,
        private MessageBusInterface $messageBus,
    ) {
    }

    /**
     * @throws EntityHasIncorrectDataException
     * @throws \DateInvalidOperationException
     * @throws EntityHasMissingDataException
     * @throws PlayerNotFoundException
     * @throws \DateMalformedIntervalStringException
     */
    public function __invoke(): void
    {
        $players = $this->timeoutPlayerFinder->find();

        foreach ($players as $player) {
            $this->messageBus->execute(new DisconnectPlayerAsyncMessage($player->id));
        }
    }
}
