<?php

namespace App\Tests\Unit\Game\Infrastructure\EventListener;

use App\Game\Application\Bus\AsyncChatMessageInterface;
use App\Game\Application\Bus\AsyncPlayerMessageInterface;
use App\Game\Application\Service\NotificationGeneratorInterface;
use App\Game\Application\UseCase\DisconnectPlayer\DisconnectPlayerAsyncMessage;
use App\Game\Infrastructure\EventListener\MessengerFailedEventListener;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Event\WorkerMessageFailedEvent;
use Symfony\Component\Messenger\Stamp\ErrorDetailsStamp;
use Symfony\Component\Serializer\SerializerInterface;

class MessengerFailedEventListenerTest extends TestCase
{
    private MockObject&NotificationGeneratorInterface $notificationGenerator;
    private MockObject&SerializerInterface $serializer;
    private MockObject&LoggerInterface $logger;

    protected function setUp(): void
    {
        parent::setUp();

        $this->notificationGenerator = $this->createMock(NotificationGeneratorInterface::class);
        $this->serializer = $this->createMock(SerializerInterface::class);
        $this->logger = $this->createMock(LoggerInterface::class);
    }

    public function testIgnoredWhenRetryFlagActive(): void
    {
        $message = $this->createMock(AsyncPlayerMessageInterface::class);
        $envelope = new Envelope($message);
        $event = new WorkerMessageFailedEvent($envelope, '', new \Exception());
        $event->setForRetry();

        $this->notificationGenerator->expects($this->never())->method('generateExceptionData');
        $this->logger->expects($this->never())->method('error');

        $listener = new MessengerFailedEventListener($this->notificationGenerator, $this->serializer, $this->logger);
        $listener->__invoke($event);
    }

    public function testIgnoredWhenMessageIsNotAboutPlayer(): void
    {
        $message = $this->createMock(AsyncChatMessageInterface::class);
        $envelope = new Envelope($message);
        $event = new WorkerMessageFailedEvent($envelope, '', new \Exception());

        $this->notificationGenerator->expects($this->never())->method('generateExceptionData');
        $this->logger->expects($this->never())->method('error');

        $listener = new MessengerFailedEventListener($this->notificationGenerator, $this->serializer, $this->logger);
        $listener->__invoke($event);
    }

    public function testProcessedWithoutException(): void
    {
        $playerId = 'playerId';

        $message = new DisconnectPlayerAsyncMessage($playerId);
        $envelope = new Envelope($message);
        $event = new WorkerMessageFailedEvent($envelope, '', new \Exception());

        $this->notificationGenerator->expects($this->once())->method('generateExceptionData')->with(
            $playerId,
            '',
            'Unknown error'
        );
        $this->logger->expects($this->once())->method('error');

        $listener = new MessengerFailedEventListener($this->notificationGenerator, $this->serializer, $this->logger);
        $listener->__invoke($event);
    }

    public function testProcessedWithException(): void
    {
        $playerId = 'playerId';
        $exceptionClass = '\MyExceptionClass';
        $exceptionMessage = 'My exception message';

        $message = new DisconnectPlayerAsyncMessage($playerId);
        $envelope = new Envelope($message, [
            new ErrorDetailsStamp($exceptionClass, '', $exceptionMessage),
        ]);
        $event = new WorkerMessageFailedEvent($envelope, '', new \Exception());

        $this->notificationGenerator->expects($this->once())->method('generateExceptionData')->with(
            $playerId,
            $exceptionClass,
            $exceptionMessage
        );
        $this->logger->expects($this->never())->method('error');

        $listener = new MessengerFailedEventListener($this->notificationGenerator, $this->serializer, $this->logger);
        $listener->__invoke($event);
    }
}
