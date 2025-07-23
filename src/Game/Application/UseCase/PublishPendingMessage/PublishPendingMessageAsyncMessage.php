<?php

namespace App\Game\Application\UseCase\PublishPendingMessage;

use App\SharedContext\Application\Bus\AsyncPendingMessageInterface;

final readonly class PublishPendingMessageAsyncMessage implements AsyncPendingMessageInterface
{
    public function __construct(
        public private(set) string $worldId,
        public private(set) string $levelName,
    ) {
    }
}
