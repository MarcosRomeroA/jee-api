<?php

declare(strict_types=1);

namespace App\Apps\Console;

use App\Contexts\Shared\Domain\CQRS\Command\CommandBus;
use App\Contexts\Web\User\Application\CleanupExpiredEmailVerifications\CleanupExpiredEmailVerificationsCommand as CleanupCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:cleanup-expired-email-verifications',
    description: 'Removes email verification records that expired more than 24 hours ago',
)]
final class CleanupExpiredEmailVerificationsCommand extends Command
{
    public function __construct(
        private readonly CommandBus $commandBus
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('Cleaning up expired email verifications');

        $command = new CleanupCommand();
        $deletedCount = $this->commandBus->dispatch($command);

        if ($deletedCount > 0) {
            $io->success(sprintf('Successfully deleted %d expired email verification(s).', $deletedCount));
        } else {
            $io->info('No expired email verifications to clean up.');
        }

        return Command::SUCCESS;
    }
}
