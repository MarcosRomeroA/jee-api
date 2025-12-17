<?php

declare(strict_types=1);

namespace App\Apps\Console;

use App\Contexts\Shared\Domain\CQRS\Command\CommandBus;
use App\Contexts\Web\Tournament\Application\FinalizeActiveTournaments\FinalizeActiveTournamentsCommand as FinalizeCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:finalize-active-tournaments',
    description: 'Finalizes all active tournaments that have ended',
)]
final class FinalizeActiveTournamentsCommand extends Command
{
    public function __construct(
        private readonly CommandBus $commandBus
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('Finalizing active tournaments that have ended');

        $command = new FinalizeCommand();
        $this->commandBus->dispatch($command);

        $io->success('Active tournaments finalization process completed.');

        return Command::SUCCESS;
    }
}
