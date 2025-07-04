<?php

namespace App\Tests\Integration\Game\Infrastructurre\Repository;

use App\Game\Domain\Exception\EntityHasIncorrectDataException;
use App\Game\Domain\Exception\EntityHasMissingDataException;
use App\Game\Domain\Exception\WorldNotFoundException;
use App\Game\Domain\Model\Entity\Player;
use App\Game\Domain\Model\Entity\World;
use App\Game\Domain\Model\Repository\PlayerRepositoryInterface;
use App\Game\Domain\Model\Repository\WorldRepositoryInterface;
use App\Game\Infrastructure\Repository\PlayerRepository;
use App\Game\Infrastructure\Repository\WorldRepository;
use App\SharedContext\Domain\Model\DatabaseKeys;
use App\SharedContext\Domain\Model\ValueObject\Vector;
use App\SharedContext\Infrastructure\Database\RedisDatabase;
use App\Tests\_Helper\RedisHelperTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Clock\Clock;

final class WorldRepositoryTest extends KernelTestCase
{
    use RedisHelperTrait;

    private RedisDatabase $redisDatabase;
    private PlayerRepositoryInterface $playerRepository;
    private WorldRepositoryInterface $worldRepository;

    protected function setUp(): void
    {
        self::bootKernel();

        $container = self::getContainer();

        /** @var RedisDatabase $redisDatabase */
        $redisDatabase = $container->get(RedisDatabase::class);
        $this->redisDatabase = $redisDatabase;

        $this->playerRepository = new PlayerRepository($this->redisDatabase, new Clock());
        $this->worldRepository = new WorldRepository($this->redisDatabase, $this->playerRepository);
    }

    protected function tearDown(): void
    {
        $this->deleteTestKeys($this->redisDatabase);
    }

    public function testSaveAndFind(): void
    {
        $player1 = new Player('playerId1', 'playerName1', new Vector(0, 1), new \DateTimeImmutable(), 'worldId');
        $player2 = new Player('playerId2', 'playerName2', new Vector(2, 3), new \DateTimeImmutable(), 'worldId');
        $world = new World('worldId', 'worldName', [$player1, $player2]);

        $this->playerRepository->save($player1);
        $this->playerRepository->save($player2);
        $this->worldRepository->save($world);

        $retrievedWorld = $this->worldRepository->find($world->id);

        $this->assertSame($world->id, $retrievedWorld->id);
        $this->assertSame($world->name, $retrievedWorld->name);
        $this->assertSame($world->getPlayersCount(), $retrievedWorld->getPlayersCount());

        for ($playerIndex = 0; $playerIndex < $world->getPlayersCount(); ++$playerIndex) {
            $this->assertSame($world->getPlayers()[$playerIndex]->id, $retrievedWorld->getPlayers()[$playerIndex]->id);
            $this->assertSame($world->getPlayers()[$playerIndex]->name, $retrievedWorld->getPlayers()[$playerIndex]->name);
            $this->assertSame((string) $world->getPlayers()[$playerIndex]->position, (string) $retrievedWorld->getPlayers()[$playerIndex]->position);
        }
    }

    public function testFailureWhenSearchingForANonExistingWorld(): void
    {
        $this->expectException(WorldNotFoundException::class);
        $this->worldRepository->find('non-existing-world-id');
    }

    public function testFailureWhenWorldHasNonExistingPlayer(): void
    {
        $player = new Player('playerId', 'playerName', new Vector(2, 3), new \DateTimeImmutable(), 'worldId');
        $world = new World('worldId', 'worldName', [$player]);

        // INFO - Not saving the player into Redis
        $this->worldRepository->save($world);

        $this->expectException(EntityHasIncorrectDataException::class);
        $this->worldRepository->find($world->id);
    }

    public function testFailureWhenSearchingForWorldThatHasIncompleteData(): void
    {
        $world = new World('worldId', 'worldName', []);

        $this->worldRepository->save($world);

        $this->redisDatabase->deleteHashField(
            sprintf(DatabaseKeys::WORLD_KEY, $world->id),
            'name'
        );

        $this->expectException(EntityHasMissingDataException::class);
        $this->worldRepository->find($world->id);
    }

    public function testFindAll(): void
    {
        $this->assertCount(0, $this->worldRepository->findAll());

        $this->worldRepository->save(new World('worldId1', 'worldName1', []));
        $this->assertCount(1, $this->worldRepository->findAll());

        $this->worldRepository->save(new World('worldId_2', 'worldName2', []));
        $this->assertCount(2, $this->worldRepository->findAll());
    }
}
