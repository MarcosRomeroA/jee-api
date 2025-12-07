<?php

declare(strict_types=1);

namespace App\Apps\Console;

use App\Contexts\Web\User\Domain\UserRepository;
use App\Contexts\Web\User\Infrastructure\Service\Image\ProfileImageOptimizer;
use App\Contexts\Web\User\Infrastructure\Service\Image\ProfileImageUploader;
use Doctrine\ORM\EntityManagerInterface;
use League\Flysystem\FilesystemOperator;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;

#[AsCommand(
    name: 'app:migrate-profile-images',
    description: 'Migrates existing profile images to the new structure with optimized thumbnails',
)]
final class MigrateProfileImagesToNewStructureCommand extends Command
{
    private const string OLD_CONTEXT = 'jee/user/profile';
    private string $projectDir;

    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly FilesystemOperator $defaultStorage,
        private readonly ProfileImageOptimizer $optimizer,
        private readonly ProfileImageUploader $uploader,
        private readonly EntityManagerInterface $entityManager,
        private readonly LoggerInterface $logger,
        string $projectDir,
    ) {
        $this->projectDir = $projectDir;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Run without making changes')
            ->addOption('limit', 'l', InputOption::VALUE_REQUIRED, 'Limit the number of users to migrate', null);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $dryRun = $input->getOption('dry-run');
        $limit = $input->getOption('limit') ? (int) $input->getOption('limit') : null;

        $io->title('Migrating profile images to new structure');

        if ($dryRun) {
            $io->warning('Running in dry-run mode. No changes will be made.');
        }

        // Get all users with profile images
        $users = $this->userRepository->findAllWithProfileImage($limit);
        $total = count($users);

        if ($total === 0) {
            $io->info('No users with profile images found.');
            return Command::SUCCESS;
        }

        $io->info(sprintf('Found %d users with profile images to migrate.', $total));

        $migrated = 0;
        $errors = 0;
        $skipped = 0;
        $filesystem = new Filesystem();
        $tempDir = $this->projectDir . '/var/tmp/migration';

        if (!$filesystem->exists($tempDir)) {
            $filesystem->mkdir($tempDir, 0755);
        }

        $io->progressStart($total);

        foreach ($users as $user) {
            $userId = $user->getId()->value();
            $oldFilename = $user->getProfileImage()->value();

            if (empty($oldFilename)) {
                $skipped++;
                $io->progressAdvance();
                continue;
            }

            try {
                // Check if already migrated (avatar.webp exists in new path)
                $newPath = 'jee/user/profile/' . $userId . '/avatar.webp';
                if ($this->defaultStorage->fileExists($newPath)) {
                    $this->logger->info('User already migrated, skipping', ['userId' => $userId]);
                    $skipped++;
                    $io->progressAdvance();
                    continue;
                }

                // Download old image
                $oldPath = self::OLD_CONTEXT . '/' . $oldFilename;

                if (!$this->defaultStorage->fileExists($oldPath)) {
                    $this->logger->warning('Old image not found', [
                        'userId' => $userId,
                        'oldPath' => $oldPath,
                    ]);
                    $skipped++;
                    $io->progressAdvance();
                    continue;
                }

                if (!$dryRun) {
                    $imageContent = $this->defaultStorage->read($oldPath);
                    $tempFile = $tempDir . '/' . $userId . '_' . basename($oldFilename);
                    file_put_contents($tempFile, $imageContent);

                    // Optimize and upload to new structure
                    $result = $this->optimizer->optimize($tempFile);
                    $this->uploader->upload($result, $userId);

                    // Update user avatarUpdatedAt
                    $user->updateAvatar();
                    $this->entityManager->flush();

                    // Clean up temp file
                    if ($filesystem->exists($tempFile)) {
                        $filesystem->remove($tempFile);
                    }

                    $this->logger->info('Successfully migrated user profile image', [
                        'userId' => $userId,
                        'oldPath' => $oldPath,
                        'newPath' => $newPath,
                    ]);
                }

                $migrated++;
            } catch (\Throwable $e) {
                $errors++;
                $this->logger->error('Failed to migrate user profile image', [
                    'userId' => $userId,
                    'error' => $e->getMessage(),
                ]);
                $io->error(sprintf('Error migrating user %s: %s', $userId, $e->getMessage()));
            }

            $io->progressAdvance();
        }

        $io->progressFinish();

        // Clean up temp directory
        if ($filesystem->exists($tempDir)) {
            $filesystem->remove($tempDir);
        }

        $io->newLine(2);
        $io->definitionList(
            ['Total users' => $total],
            ['Migrated' => $migrated],
            ['Skipped' => $skipped],
            ['Errors' => $errors],
        );

        if ($errors > 0) {
            $io->warning('Migration completed with errors. Check logs for details.');
            return Command::FAILURE;
        }

        $io->success('Migration completed successfully.');
        return Command::SUCCESS;
    }
}
