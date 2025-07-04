<?php

namespace App\Tests\Integration\Game\Infrastructure\Repository;

use App\Game\Domain\Exception\EntityHasIncorrectDataException;
use App\Game\Domain\Exception\EntityHasMissingDataException;
use App\Game\Domain\Exception\PlayerNotFoundException;
use App\Game\Domain\Model\Entity\Player;
use App\Game\Domain\Model\Repository\PlayerRepositoryInterface;
use App\Game\Infrastructure\Repository\PlayerRepository;
use App\SharedContext\Domain\Model\DatabaseKeys;
use App\SharedContext\Domain\Model\ValueObject\Vector;
use App\SharedContext\Infrastructure\Database\RedisDatabase;
use App\Tests\_Helper\RedisHelperTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Clock\Clock;

class PlayerRepositoryTest extends KernelTestCase
{
    use RedisHelperTrait;

    private RedisDatabase $redisDatabase;
    private PlayerRepositoryInterface $playerRepository;

    protected function setUp(): void
    {
        self::bootKernel();

        $container = self::getContainer();

        /** @var RedisDatabase $redisDatabase */
        $redisDatabase = $container->get(RedisDatabase::class);
        $this->redisDatabase = $redisDatabase;

        $this->playerRepository = new PlayerRepository($this->redisDatabase, new Clock());
    }

    protected function tearDown(): void
    {
        $this->deleteTestKeys($this->redisDatabase);
    }

    public function testSaveAndFind(): void
    {
        $player = new Player('playerId1', 'playerName1', new Vector(0, 1), new \DateTimeImmutable(), 'worldId', 'levelName');

        $this->playerRepository->save($player);

        $retrievedPlayer = $this->playerRepository->find($player->id);

        $this->assertSame($player->id, $retrievedPlayer->id);
        $this->assertSame($player->name, $retrievedPlayer->name);
        $this->assertSame((string) $player->position, (string) $retrievedPlayer->position);
        $this->assertSame($player->worldId, $retrievedPlayer->worldId);
        $this->assertSame($player->levelName, $retrievedPlayer->levelName);
    }

    public function testFindAll(): void
    {
        $player1 = new Player('playerId1', 'playerName1', new Vector(0, 1), new \DateTimeImmutable(), 'worldId1', 'levelName');
        $player2 = new Player('playerId2', 'playerName2', new Vector(0, 1), new \DateTimeImmutable(), 'worldId1', 'levelName');
        $player3 = new Player('playerId3', 'playerName3', new Vector(0, 1), new \DateTimeImmutable(), 'worldId2', 'levelName');

        $this->playerRepository->save($player1);
        $this->playerRepository->save($player2);
        $this->playerRepository->save($player3);

        $players = $this->playerRepository->findAll();

        $this->assertCount(3, $players);
        $this->assertSame($player1->id, $players[0]->id);
        $this->assertSame($player2->id, $players[1]->id);
        $this->assertSame($player3->id, $players[2]->id);
    }

    public function testFailureWhenSearchingForANonExistingPlayer(): void
    {
        $this->expectException(PlayerNotFoundException::class);
        $this->playerRepository->find('non-existing-player-id');
    }

    public function testFailureWhenSearchingForPlayerThatHasIncorrectData(): void
    {
        $player = new Player('playerId1', 'playerName1', new Vector(0, 1), new \DateTimeImmutable(), 'worldId', 'levelName');

        $this->playerRepository->save($player);

        $this->redisDatabase->setHashValue(
            sprintf(DatabaseKeys::PLAYER_KEY, $player->id),
            'position',
            '-1,-1'
        );

        $this->expectException(EntityHasIncorrectDataException::class);
        $this->playerRepository->find($player->id);
    }

    public function testFailureWhenSearchingForPlayerThatHasIncompleteData(): void
    {
        $player = new Player('playerId', 'playerName', new Vector(0, 1), new \DateTimeImmutable(), 'worldId', 'levelName');

        $this->playerRepository->save($player);

        $this->redisDatabase->deleteHashField(
            sprintf(DatabaseKeys::PLAYER_KEY, $player->id),
            'name'
        );

        $this->expectException(EntityHasMissingDataException::class);
        $this->playerRepository->find($player->id);
    }

    public function testDeletePlayer(): void
    {
        $player1 = new Player('playerId1', 'playerName1', new Vector(0, 1), new \DateTimeImmutable(), 'worldId', 'levelName');
        $player2 = new Player('playerId2', 'playerName2', new Vector(0, 1), new \DateTimeImmutable(), 'worldId', 'levelName');

        $this->playerRepository->save($player1);
        $this->playerRepository->save($player2);

        $this->playerRepository->find($player1->id);
        $this->playerRepository->find($player2->id);

        $this->playerRepository->delete($player2);

        $this->playerRepository->find($player1->id);

        $this->expectException(PlayerNotFoundException::class);
        $this->playerRepository->find($player2->id);
    }
}
