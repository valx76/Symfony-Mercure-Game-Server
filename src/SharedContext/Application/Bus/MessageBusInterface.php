<?php

namespace App\SharedContext\Application\Bus;

interface MessageBusInterface
{
    public function execute(BaseAsyncMessageInterface|SyncMessageInterface $message): void;
}
