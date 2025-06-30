<?php

namespace App\Tests\Behat;

use Behat\Behat\Context\Context;
use Behat\Hook\BeforeScenario;
use Behat\Step\Then;
use PHPUnit\Framework\Assert;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Messenger\Exception\HandlerFailedException;

final class ExceptionContext implements Context, EventSubscriberInterface
{
    private ?\Throwable $lastException = null;

    #[BeforeScenario]
    public function resetLastException(): void
    {
        $this->lastException = null;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => 'onKernelException', // TODO - Check priority
        ];
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $this->lastException = $event->getThrowable();
    }

    #[Then('/^An exception of type "([^"]*)" should be thrown$/')]
    public function anExceptionOfTypeShouldBeThrown(string $type): void
    {
        if (null === $this->lastException) {
            Assert::fail('No exception was thrown');
        }

        if ($this->lastException instanceof HandlerFailedException) {
            if (array_any($this->lastException->getWrappedExceptions(), fn ($exception) => $exception instanceof $type)) {
                return;
            }
        }

        if (!$this->lastException instanceof $type) {
            Assert::fail(sprintf('Expected exception of type "%s", but got "%s"', $type, get_class($this->lastException)));
        }
    }
}
