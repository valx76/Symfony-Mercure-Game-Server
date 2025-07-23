<?php

namespace App\Game\Infrastructure\Scheduler;

use App\Game\Application\UseCase\PublishPendingMessage\PublishPendingMessageAsyncMessage;
use App\Game\Domain\Model\Repository\PendingLevelMessageRepositoryInterface;
use App\SharedContext\Application\Bus\MessageBusInterface;
use Symfony\Component\Scheduler\Attribute\AsPeriodicTask;

#[AsPeriodicTask(frequency: '%env(int:TASK_PUBLISH_PENDING_MESSAGE_SECS)% second')]
final readonly class PublishPendingMessageSchedule
{
    public function __construct(
        private MessageBusInterface $messageBus,
        private PendingLevelMessageRepositoryInterface $pendingMessageRepository,
    ) {
    }

    public function __invoke(): void
    {
        while (true) {
            $value = $this->pendingMessageRepository->pop();

            if (null === $value) {
                break;
            }

            $explodedValue = explode(',', $value);
            $worldId = $explodedValue[0];
            $levelName = $explodedValue[1];

            $this->messageBus->execute(new PublishPendingMessageAsyncMessage($worldId, $levelName));
        }
    }
}
