<?php

namespace App\Game\Presentation\Console;

use App\Game\Domain\Model\Entity\World;
use App\Game\Domain\Model\Repository\WorldRepositoryInterface;
use App\SharedContext\Domain\Service\UuidGeneratorInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:generate-world',
    description: 'Generate a new world and its levels',
)]
final class GenerateWorldCommand extends Command
{
    public function __construct(
        private readonly UuidGeneratorInterface $uuidGenerator,
        private readonly WorldRepositoryInterface $worldRepository,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('name', InputArgument::REQUIRED, 'World\'s name')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var string $worldName */
        $worldName = $input->getArgument('name');

        $world = new World(
            $this->uuidGenerator->generate(),
            $worldName,
            [],
        );
        $this->worldRepository->save($world);

        $output->writeln(
            sprintf('The new world "%s" has been generated.', $worldName)
        );

        return Command::SUCCESS;
    }
}
