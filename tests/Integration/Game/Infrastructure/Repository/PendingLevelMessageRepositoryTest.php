<?php

namespace App\Tests\Integration\Game\Infrastructure\Repository;

use App\Game\Domain\Model\DatabaseKeys;
use App\Game\Domain\Model\Entity\World;
use App\Game\Domain\Model\Repository\PendingLevelMessageRepositoryInterface;
use App\Game\Infrastructure\Repository\PendingLevelMessageRepository;
use App\SharedContext\Infrastructure\Database\RedisDatabase;
use App\Tests\_Helper\RedisHelperTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class PendingLevelMessageRepositoryTest extends KernelTestCase
{
    use RedisHelperTrait;

    private RedisDatabase $redisDatabase;
    private PendingLevelMessageRepositoryInterface $pendingLevelMessageRepository;

    protected function setUp(): void
    {
        self::bootKernel();

        $container = self::getContainer();

        /** @var RedisDatabase $redisDatabase */
        $redisDatabase = $container->get(RedisDatabase::class);
        $this->redisDatabase = $redisDatabase;

        $this->pendingLevelMessageRepository = new PendingLevelMessageRepository($this->redisDatabase);
    }

    protected function tearDown(): void
    {
        $this->deleteTestKeys($this->redisDatabase);
    }

    public function testNullWhenPopEmptySet(): void
    {
        $this->assertNull($this->pendingLevelMessageRepository->pop());

        $world = new World('worldId', 'worldName', []);
        $this->pendingLevelMessageRepository->push($world, 'levelName');

        $this->assertNotNull($this->pendingLevelMessageRepository->pop());
        $this->assertNull($this->pendingLevelMessageRepository->pop());
    }

    public function testPushPopCorrectItem(): void
    {
        $world1 = new World('worldId1', 'worldName1', []);
        $world2 = new World('worldId2', 'worldName2', []);

        $this->pendingLevelMessageRepository->push($world1, 'levelName1');
        $this->pendingLevelMessageRepository->push($world2, 'levelName2');

        $this->assertSame(
            sprintf(DatabaseKeys::PENDING_MESSAGE_FORMAT, $world2->id, 'levelName2'),
            $this->pendingLevelMessageRepository->pop()
        );

        $this->assertSame(
            sprintf(DatabaseKeys::PENDING_MESSAGE_FORMAT, $world1->id, 'levelName1'),
            $this->pendingLevelMessageRepository->pop()
        );
    }
}
