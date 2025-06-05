<?php

namespace App\SharedContext\Application\Bus;

interface MessageBusInterface
{
    public function execute(AsyncMessageInterface|SyncMessageInterface $message): void;
}
