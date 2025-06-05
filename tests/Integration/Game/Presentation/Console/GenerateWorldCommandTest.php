<?php

namespace App\Tests\Integration\Game\Presentation\Console;

use App\Game\Domain\Model\Repository\PlayerRepositoryInterface;
use App\Game\Domain\Model\Repository\WorldRepositoryInterface;
use App\Game\Infrastructure\Repository\PlayerRepository;
use App\Game\Infrastructure\Repository\WorldRepository;
use App\SharedContext\Infrastructure\Database\RedisDatabase;
use App\Tests\_Helper\RedisHelperTrait;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class GenerateWorldCommandTest extends KernelTestCase
{
    use RedisHelperTrait;


    private RedisDatabase $redisDatabase;
    private PlayerRepositoryInterface $playerRepository;
    private WorldRepositoryInterface $worldRepository;
    private Application $application;

    protected function setUp(): void
    {
        self::bootKernel();

        $container = self::getContainer();

        /** @phpstan-ignore argument.type */
        $this->application = new Application(self::$kernel);

        /** @var RedisDatabase $redisDatabase */
        $redisDatabase = $container->get(RedisDatabase::class);
        $this->redisDatabase = $redisDatabase;

        $this->playerRepository = new PlayerRepository($this->redisDatabase);
        $this->worldRepository = new WorldRepository($this->redisDatabase, $this->playerRepository);
    }

    protected function tearDown(): void
    {
        $this->deleteTestKeys($this->redisDatabase);
    }

    public function testCommand(): void
    {
        $worldName = 'worldName';

        $command = $this->application->find('app:generate-world');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'name' => $worldName,
        ]);

        $commandTester->assertCommandIsSuccessful();

        $this->assertStringContainsString(
            sprintf('The new world "%s" has been generated.', $worldName),
            $commandTester->getDisplay()
        );

        $isWorldFound = false;
        foreach ($this->worldRepository->findAll() as $world) {
            if ($world->name === $worldName) {
                $isWorldFound = true;
                break;
            }
        }
        $this->assertTrue($isWorldFound);
    }
}
