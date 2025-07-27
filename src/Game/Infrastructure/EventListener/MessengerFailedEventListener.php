<?php

namespace App\Game\Infrastructure\EventListener;

use App\Game\Application\Service\NotificationGeneratorInterface;
use App\Game\Domain\Exception\NotificationException;
use App\SharedContext\Application\Bus\AsyncPlayerMessageInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Messenger\Event\WorkerMessageFailedEvent;
use Symfony\Component\Messenger\Stamp\ErrorDetailsStamp;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\SerializerInterface;

#[AsEventListener(WorkerMessageFailedEvent::class)]
final readonly class MessengerFailedEventListener
{
    private const string UNKNOWN_ERROR_MESSAGE = 'Unknown error';

    public function __construct(
        private NotificationGeneratorInterface $notificationGenerator,
        private SerializerInterface $serializer,
        private LoggerInterface $logger,
    ) {
    }

    /**
     * @throws NotificationException
     * @throws ExceptionInterface
     */
    public function __invoke(WorkerMessageFailedEvent $event): void
    {
        // Ignore if the message will be retried later
        if ($event->willRetry()) {
            return;
        }

        $envelope = $event->getEnvelope();
        $message = $envelope->getMessage();

        // Only handle async messages having a 'playerId' property
        if (!$message instanceof AsyncPlayerMessageInterface) {
            return;
        }

        $exceptionDetails = $envelope->last(ErrorDetailsStamp::class);
        $exceptionClass = $exceptionDetails?->getExceptionClass() ?? '';
        $exceptionMessage = $exceptionDetails?->getExceptionMessage() ?? '';

        $this->notificationGenerator->generateExceptionData(
            $message->playerId,
            $exceptionClass,
            strlen($exceptionMessage) > 0 ? $exceptionMessage : self::UNKNOWN_ERROR_MESSAGE
        );

        // Something is really wrong here, so logging it
        if (empty($exceptionClass)) {
            $serializedExceptionDetails = $this->serializer->serialize($exceptionDetails, 'json');

            $this->logger->error(
                sprintf("No exceptionClass in Messenger error!\nMessage: %s\nDetails: %s\n\n", $exceptionMessage, $serializedExceptionDetails)
            );
        }
    }
}
