<?php

namespace App\Tests\Integration\Game\Application\Service;

use App\Game\Application\Service\NotificationGenerator;
use App\Game\Domain\Model\Entity\Level\Level1;
use App\Game\Domain\Model\Entity\Level\Level2;
use App\Game\Domain\Model\Entity\PlayerNotificationTypeEnum;
use App\Game\Domain\Model\Entity\World;
use App\Game\Domain\Service\LevelFactory;
use App\Game\Domain\Service\LevelNormalizerInterface;
use App\SharedContext\Domain\Model\MercureTopics;
use App\SharedContext\Infrastructure\Mercure\MercurePublisher;
use App\Tests\_Helper\Stub\HubStub;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class NotificationGeneratorTest extends KernelTestCase
{
    private LevelNormalizerInterface $levelNormalizer;
    private LevelFactory $levelFactory;
    private HubStub $mercureHub;
    private NotificationGenerator $notificationGenerator;

    protected function setUp(): void
    {
        self::bootKernel();

        $container = self::getContainer();

        /** @var LevelNormalizerInterface $levelNormalizer */
        $levelNormalizer = $container->get(LevelNormalizerInterface::class);
        $this->levelNormalizer = $levelNormalizer;

        /** @var LevelFactory $levelFactory */
        $levelFactory = $container->get(LevelFactory::class);
        $this->levelFactory = $levelFactory;

        /** @var HubStub $mercureHub */
        $mercureHub = $container->get(HubStub::class);
        $this->mercureHub = $mercureHub;

        $mercurePublisher = new MercurePublisher($mercureHub);

        $this->notificationGenerator = new NotificationGenerator($levelNormalizer, $mercurePublisher);
    }

    protected function tearDown(): void
    {
        $this->mercureHub->updates = [];
    }

    public function testGenerateLevelData(): void
    {
        $world = new World('worldId', 'worldName', []);
        $level = $this->levelFactory->create(Level1::class);

        $this->notificationGenerator->generateLevelData($world, $level);

        $expectedTopic = sprintf(MercureTopics::LEVEL, $world->id, $level::class);
        $expectedData = json_encode($this->levelNormalizer->normalize($world, $level), JSON_THROW_ON_ERROR);

        $this->assertSame($expectedTopic, $this->mercureHub->updates[0]->getTopics()[0]);
        $this->assertSame($expectedData, $this->mercureHub->updates[0]->getData());
    }

    public function testGenerateExceptionData(): void
    {
        $playerId = 'playerId';
        $exceptionClass = 'exceptionClass';
        $message = 'message';

        $this->notificationGenerator->generateExceptionData($playerId, $exceptionClass, $message);

        $expectedTopic = sprintf(MercureTopics::PLAYER, $playerId);
        $expectedData = json_encode([
            'TYPE' => PlayerNotificationTypeEnum::EXCEPTION,
            'MESSAGE' => $message,
            'EXCEPTION' => $exceptionClass,
        ], JSON_THROW_ON_ERROR);

        $this->assertSame($expectedTopic, $this->mercureHub->updates[0]->getTopics()[0]);
        $this->assertSame($expectedData, $this->mercureHub->updates[0]->getData());
    }

    public function testGenerateMessageData(): void
    {
        $world = new World('worldId', 'worldName', []);
        $level = $this->levelFactory->create(Level1::class);
        $playerId = 'playerId';
        $message = 'message';

        $this->notificationGenerator->generateMessageData($world, $level, $playerId, $message);

        $expectedTopic = sprintf(MercureTopics::MESSAGE, $world->id, $level::class);
        $expectedData = json_encode([
            'PLAYER' => $playerId,
            'MESSAGE' => $message,
        ], JSON_THROW_ON_ERROR);

        $this->assertSame($expectedTopic, $this->mercureHub->updates[0]->getTopics()[0]);
        $this->assertSame($expectedData, $this->mercureHub->updates[0]->getData());
    }

    public function testGenerateDisconnectData(): void
    {
        $playerId = 'playerId';

        $this->notificationGenerator->generateDisconnectData($playerId);

        $expectedTopic = sprintf(MercureTopics::PLAYER, $playerId);
        $expectedData = json_encode([
            'TYPE' => PlayerNotificationTypeEnum::DISCONNECT,
        ], JSON_THROW_ON_ERROR);

        $this->assertSame($expectedTopic, $this->mercureHub->updates[0]->getTopics()[0]);
        $this->assertSame($expectedData, $this->mercureHub->updates[0]->getData());
    }

    public function testGenerateLevelChangeData(): void
    {
        $playerId = 'playerId';
        $targetLevelName = Level2::class;

        $this->notificationGenerator->generateLevelChangeData($playerId, $targetLevelName);

        $expectedTopic = sprintf(MercureTopics::PLAYER, $playerId);
        $expectedData = json_encode([
            'TYPE' => PlayerNotificationTypeEnum::LEVEL_CHANGE,
            'LEVEL' => $targetLevelName,
        ], JSON_THROW_ON_ERROR);

        $this->assertSame($expectedTopic, $this->mercureHub->updates[0]->getTopics()[0]);
        $this->assertSame($expectedData, $this->mercureHub->updates[0]->getData());
    }
}
