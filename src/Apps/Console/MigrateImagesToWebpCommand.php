<?php

declare(strict_types=1);

namespace App\Apps\Console;

use App\Contexts\Shared\Infrastructure\Image\ImageOptimizer;
use App\Contexts\Web\Post\Domain\PostResource;
use App\Contexts\Web\Team\Domain\Team;
use App\Contexts\Web\Team\Domain\ValueObject\TeamBackgroundImageValue;
use App\Contexts\Web\Team\Domain\ValueObject\TeamImageValue;
use App\Contexts\Web\Tournament\Domain\Tournament;
use App\Contexts\Web\User\Domain\User;
use App\Contexts\Web\User\Domain\ValueObject\BackgroundImageValue;
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
use Symfony\Component\HttpKernel\KernelInterface;

#[AsCommand(
    name: 'app:migrate-images-to-webp',
    description: 'Migrates existing images (posts, teams, tournaments) to WebP format',
)]
final class MigrateImagesToWebpCommand extends Command
{
    private const string PREFIX = 'jee/';

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly FilesystemOperator $defaultStorage,
        private readonly ImageOptimizer $imageOptimizer,
        private readonly LoggerInterface $logger,
        private readonly KernelInterface $kernel,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Run without making changes')
            ->addOption('type', 't', InputOption::VALUE_REQUIRED, 'Type to migrate: posts, teams, tournaments, users, or all', 'all')
            ->addOption('limit', 'l', InputOption::VALUE_REQUIRED, 'Limit the number of items to migrate', null);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $dryRun = $input->getOption('dry-run');
        $type = $input->getOption('type');
        $limit = $input->getOption('limit') ? (int) $input->getOption('limit') : null;

        $io->title('Migrating images to WebP format');

        if ($dryRun) {
            $io->warning('Running in dry-run mode. No changes will be made.');
        }

        $tempDir = $this->kernel->getProjectDir() . '/var/tmp/webp-migration';
        $filesystem = new Filesystem();

        if (!$filesystem->exists($tempDir)) {
            $filesystem->mkdir($tempDir, 0755);
        }

        $totalMigrated = 0;
        $totalErrors = 0;
        $totalSkipped = 0;

        try {
            if ($type === 'all' || $type === 'posts') {
                $result = $this->migratePosts($io, $dryRun, $limit, $tempDir, $filesystem);
                $totalMigrated += $result['migrated'];
                $totalErrors += $result['errors'];
                $totalSkipped += $result['skipped'];
            }

            if ($type === 'all' || $type === 'teams') {
                $result = $this->migrateTeams($io, $dryRun, $limit, $tempDir, $filesystem);
                $totalMigrated += $result['migrated'];
                $totalErrors += $result['errors'];
                $totalSkipped += $result['skipped'];
            }

            if ($type === 'all' || $type === 'tournaments') {
                $result = $this->migrateTournaments($io, $dryRun, $limit, $tempDir, $filesystem);
                $totalMigrated += $result['migrated'];
                $totalErrors += $result['errors'];
                $totalSkipped += $result['skipped'];
            }

            if ($type === 'all' || $type === 'users') {
                $result = $this->migrateUsers($io, $dryRun, $limit, $tempDir, $filesystem);
                $totalMigrated += $result['migrated'];
                $totalErrors += $result['errors'];
                $totalSkipped += $result['skipped'];
            }
        } finally {
            if ($filesystem->exists($tempDir)) {
                $filesystem->remove($tempDir);
            }
        }

        $io->newLine(2);
        $io->definitionList(
            ['Total migrated' => $totalMigrated],
            ['Total skipped' => $totalSkipped],
            ['Total errors' => $totalErrors],
        );

        if ($totalErrors > 0) {
            $io->warning('Migration completed with errors. Check logs for details.');
            return Command::FAILURE;
        }

        $io->success('Migration completed successfully.');
        return Command::SUCCESS;
    }

    private function migratePosts(SymfonyStyle $io, bool $dryRun, ?int $limit, string $tempDir, Filesystem $filesystem): array
    {
        $io->section('Migrating Post Resources');

        $qb = $this->entityManager->createQueryBuilder()
            ->select('pr')
            ->from(PostResource::class, 'pr')
            ->where('pr.resourceType = :imageType')
            ->andWhere('pr.filename NOT LIKE :webpPattern')
            ->setParameter('imageType', PostResource::RESOURCE_TYPE_IMAGE)
            ->setParameter('webpPattern', '%.webp');

        if ($limit !== null) {
            $qb->setMaxResults($limit);
        }

        $resources = $qb->getQuery()->getResult();
        $total = count($resources);

        if ($total === 0) {
            $io->info('No post images to migrate.');
            return ['migrated' => 0, 'errors' => 0, 'skipped' => 0];
        }

        $io->info(sprintf('Found %d post images to migrate.', $total));
        $io->progressStart($total);

        $migrated = 0;
        $errors = 0;
        $skipped = 0;

        foreach ($resources as $resource) {
            /** @var PostResource $resource */
            $post = $resource->getPost();
            if ($post === null) {
                $skipped++;
                $io->progressAdvance();
                continue;
            }

            $postId = $post->getId()->value();
            $oldFilename = $resource->getFilename();
            $resourceType = PostResource::getResourceTypeFromId($resource->getResourceType());
            $oldPath = self::PREFIX . "posts/$postId/$resourceType/$oldFilename";

            try {
                $newFilename = $this->migrateImage($oldPath, $tempDir, $filesystem, $dryRun);

                if ($newFilename === null) {
                    $skipped++;
                    $io->progressAdvance();
                    continue;
                }

                if (!$dryRun) {
                    $resource->setFilename($newFilename);
                    $resource->setImageUpdatedAt(new \DateTimeImmutable());
                    $this->entityManager->flush();
                }

                $migrated++;
                $this->logger->info('Migrated post image', [
                    'postId' => $postId,
                    'oldFilename' => $oldFilename,
                    'newFilename' => $newFilename,
                ]);
            } catch (\Throwable $e) {
                $errors++;
                $this->logger->error('Failed to migrate post image', [
                    'postId' => $postId,
                    'filename' => $oldFilename,
                    'error' => $e->getMessage(),
                ]);
            }

            $io->progressAdvance();
        }

        $io->progressFinish();

        return ['migrated' => $migrated, 'errors' => $errors, 'skipped' => $skipped];
    }

    private function migrateTeams(SymfonyStyle $io, bool $dryRun, ?int $limit, string $tempDir, Filesystem $filesystem): array
    {
        $io->section('Migrating Team Images');

        $qb = $this->entityManager->createQueryBuilder()
            ->select('t')
            ->from(Team::class, 't')
            ->where('t.image.value IS NOT NULL')
            ->andWhere('t.image.value != :empty')
            ->andWhere('t.image.value NOT LIKE :webpPattern')
            ->setParameter('empty', '')
            ->setParameter('webpPattern', '%.webp');

        if ($limit !== null) {
            $qb->setMaxResults($limit);
        }

        $teams = $qb->getQuery()->getResult();
        $total = count($teams);

        // Also get teams with background images
        $qbBg = $this->entityManager->createQueryBuilder()
            ->select('t')
            ->from(Team::class, 't')
            ->where('t.backgroundImage.value IS NOT NULL')
            ->andWhere('t.backgroundImage.value != :empty')
            ->andWhere('t.backgroundImage.value NOT LIKE :webpPattern')
            ->setParameter('empty', '')
            ->setParameter('webpPattern', '%.webp');

        if ($limit !== null) {
            $qbBg->setMaxResults($limit);
        }

        $teamsWithBg = $qbBg->getQuery()->getResult();

        if ($total === 0 && count($teamsWithBg) === 0) {
            $io->info('No team images to migrate.');
            return ['migrated' => 0, 'errors' => 0, 'skipped' => 0];
        }

        $io->info(sprintf('Found %d team images and %d background images to migrate.', $total, count($teamsWithBg)));

        $migrated = 0;
        $errors = 0;
        $skipped = 0;

        // Migrate main images
        if ($total > 0) {
            $io->progressStart($total);

            foreach ($teams as $team) {
                /** @var Team $team */
                $teamId = $team->getId()->value();
                $oldFilename = $team->getImage();
                $oldPath = self::PREFIX . "team/$teamId/$oldFilename";

                try {
                    $newFilename = $this->migrateImage($oldPath, $tempDir, $filesystem, $dryRun);

                    if ($newFilename === null) {
                        $skipped++;
                        $io->progressAdvance();
                        continue;
                    }

                    if (!$dryRun) {
                        $team->setImage(new TeamImageValue($newFilename));
                        $team->setImageUpdatedAt(new \DateTimeImmutable());
                        $this->entityManager->flush();
                    }

                    $migrated++;
                    $this->logger->info('Migrated team image', [
                        'teamId' => $teamId,
                        'oldFilename' => $oldFilename,
                        'newFilename' => $newFilename,
                    ]);
                } catch (\Throwable $e) {
                    $errors++;
                    $this->logger->error('Failed to migrate team image', [
                        'teamId' => $teamId,
                        'filename' => $oldFilename,
                        'error' => $e->getMessage(),
                    ]);
                }

                $io->progressAdvance();
            }

            $io->progressFinish();
        }

        // Migrate background images
        if (count($teamsWithBg) > 0) {
            $io->info('Migrating team background images...');
            $io->progressStart(count($teamsWithBg));

            foreach ($teamsWithBg as $team) {
                /** @var Team $team */
                $teamId = $team->getId()->value();
                $oldFilename = $team->getBackgroundImage();
                $oldPath = self::PREFIX . "team/$teamId/background/$oldFilename";

                try {
                    $newFilename = $this->migrateImage($oldPath, $tempDir, $filesystem, $dryRun);

                    if ($newFilename === null) {
                        $skipped++;
                        $io->progressAdvance();
                        continue;
                    }

                    if (!$dryRun) {
                        $team->setBackgroundImage(new TeamBackgroundImageValue($newFilename));
                        $this->entityManager->flush();
                    }

                    $migrated++;
                    $this->logger->info('Migrated team background image', [
                        'teamId' => $teamId,
                        'oldFilename' => $oldFilename,
                        'newFilename' => $newFilename,
                    ]);
                } catch (\Throwable $e) {
                    $errors++;
                    $this->logger->error('Failed to migrate team background image', [
                        'teamId' => $teamId,
                        'filename' => $oldFilename,
                        'error' => $e->getMessage(),
                    ]);
                }

                $io->progressAdvance();
            }

            $io->progressFinish();
        }

        return ['migrated' => $migrated, 'errors' => $errors, 'skipped' => $skipped];
    }

    private function migrateTournaments(SymfonyStyle $io, bool $dryRun, ?int $limit, string $tempDir, Filesystem $filesystem): array
    {
        $io->section('Migrating Tournament Images');

        $qb = $this->entityManager->createQueryBuilder()
            ->select('t')
            ->from(Tournament::class, 't')
            ->where('t.image IS NOT NULL')
            ->andWhere('t.image != :empty')
            ->andWhere('t.image NOT LIKE :webpPattern')
            ->setParameter('empty', '')
            ->setParameter('webpPattern', '%.webp');

        if ($limit !== null) {
            $qb->setMaxResults($limit);
        }

        $tournaments = $qb->getQuery()->getResult();
        $total = count($tournaments);

        // Also get tournaments with background images
        $qbBg = $this->entityManager->createQueryBuilder()
            ->select('t')
            ->from(Tournament::class, 't')
            ->where('t.backgroundImage IS NOT NULL')
            ->andWhere('t.backgroundImage != :empty')
            ->andWhere('t.backgroundImage NOT LIKE :webpPattern')
            ->setParameter('empty', '')
            ->setParameter('webpPattern', '%.webp');

        if ($limit !== null) {
            $qbBg->setMaxResults($limit);
        }

        $tournamentsWithBg = $qbBg->getQuery()->getResult();

        if ($total === 0 && count($tournamentsWithBg) === 0) {
            $io->info('No tournament images to migrate.');
            return ['migrated' => 0, 'errors' => 0, 'skipped' => 0];
        }

        $io->info(sprintf('Found %d tournament images and %d background images to migrate.', $total, count($tournamentsWithBg)));

        $migrated = 0;
        $errors = 0;
        $skipped = 0;

        // Migrate main images
        if ($total > 0) {
            $io->progressStart($total);

            foreach ($tournaments as $tournament) {
                /** @var Tournament $tournament */
                $tournamentId = $tournament->getId()->value();
                $oldFilename = $tournament->getImage();
                $oldPath = self::PREFIX . "tournament/$tournamentId/$oldFilename";

                try {
                    $newFilename = $this->migrateImage($oldPath, $tempDir, $filesystem, $dryRun);

                    if ($newFilename === null) {
                        $skipped++;
                        $io->progressAdvance();
                        continue;
                    }

                    if (!$dryRun) {
                        $tournament->setImage($newFilename);
                        $tournament->setImageUpdatedAt(new \DateTimeImmutable());
                        $this->entityManager->flush();
                    }

                    $migrated++;
                    $this->logger->info('Migrated tournament image', [
                        'tournamentId' => $tournamentId,
                        'oldFilename' => $oldFilename,
                        'newFilename' => $newFilename,
                    ]);
                } catch (\Throwable $e) {
                    $errors++;
                    $this->logger->error('Failed to migrate tournament image', [
                        'tournamentId' => $tournamentId,
                        'filename' => $oldFilename,
                        'error' => $e->getMessage(),
                    ]);
                }

                $io->progressAdvance();
            }

            $io->progressFinish();
        }

        // Migrate background images
        if (count($tournamentsWithBg) > 0) {
            $io->info('Migrating tournament background images...');
            $io->progressStart(count($tournamentsWithBg));

            foreach ($tournamentsWithBg as $tournament) {
                /** @var Tournament $tournament */
                $tournamentId = $tournament->getId()->value();
                $oldFilename = $tournament->getBackgroundImage();
                $oldPath = self::PREFIX . "tournament/$tournamentId/background/$oldFilename";

                try {
                    $newFilename = $this->migrateImage($oldPath, $tempDir, $filesystem, $dryRun);

                    if ($newFilename === null) {
                        $skipped++;
                        $io->progressAdvance();
                        continue;
                    }

                    if (!$dryRun) {
                        $tournament->setBackgroundImage($newFilename);
                        $this->entityManager->flush();
                    }

                    $migrated++;
                    $this->logger->info('Migrated tournament background image', [
                        'tournamentId' => $tournamentId,
                        'oldFilename' => $oldFilename,
                        'newFilename' => $newFilename,
                    ]);
                } catch (\Throwable $e) {
                    $errors++;
                    $this->logger->error('Failed to migrate tournament background image', [
                        'tournamentId' => $tournamentId,
                        'filename' => $oldFilename,
                        'error' => $e->getMessage(),
                    ]);
                }

                $io->progressAdvance();
            }

            $io->progressFinish();
        }

        return ['migrated' => $migrated, 'errors' => $errors, 'skipped' => $skipped];
    }

    private function migrateUsers(SymfonyStyle $io, bool $dryRun, ?int $limit, string $tempDir, Filesystem $filesystem): array
    {
        $io->section('Migrating User Background Images');

        $qb = $this->entityManager->createQueryBuilder()
            ->select('u')
            ->from(User::class, 'u')
            ->where('u.backgroundImage.value IS NOT NULL')
            ->andWhere('u.backgroundImage.value != :empty')
            ->andWhere('u.backgroundImage.value NOT LIKE :webpPattern')
            ->setParameter('empty', '')
            ->setParameter('webpPattern', '%.webp');

        if ($limit !== null) {
            $qb->setMaxResults($limit);
        }

        $users = $qb->getQuery()->getResult();
        $total = count($users);

        if ($total === 0) {
            $io->info('No user background images to migrate.');
            return ['migrated' => 0, 'errors' => 0, 'skipped' => 0];
        }

        $io->info(sprintf('Found %d user background images to migrate.', $total));
        $io->progressStart($total);

        $migrated = 0;
        $errors = 0;
        $skipped = 0;

        foreach ($users as $user) {
            /** @var User $user */
            $userId = $user->getId()->value();
            $oldFilename = $user->getBackgroundImage()->value();
            $oldPath = self::PREFIX . "user/$userId/background/$oldFilename";

            try {
                $newFilename = $this->migrateImage($oldPath, $tempDir, $filesystem, $dryRun);

                if ($newFilename === null) {
                    $skipped++;
                    $io->progressAdvance();
                    continue;
                }

                if (!$dryRun) {
                    $user->setBackgroundImage(new BackgroundImageValue($newFilename));
                    $user->setBackgroundImageUpdatedAt(new \DateTimeImmutable());
                    $this->entityManager->flush();
                }

                $migrated++;
                $this->logger->info('Migrated user background image', [
                    'userId' => $userId,
                    'oldFilename' => $oldFilename,
                    'newFilename' => $newFilename,
                ]);
            } catch (\Throwable $e) {
                $errors++;
                $this->logger->error('Failed to migrate user background image', [
                    'userId' => $userId,
                    'filename' => $oldFilename,
                    'error' => $e->getMessage(),
                ]);
            }

            $io->progressAdvance();
        }

        $io->progressFinish();

        return ['migrated' => $migrated, 'errors' => $errors, 'skipped' => $skipped];
    }

    /**
     * Downloads image from R2, optimizes to WebP, uploads new file, and deletes old file.
     *
     * @return string|null The new filename, or null if skipped (already webp or not found)
     */
    private function migrateImage(string $oldPath, string $tempDir, Filesystem $filesystem, bool $dryRun): ?string
    {
        // Check if file exists
        if (!$this->defaultStorage->fileExists($oldPath)) {
            $this->logger->warning('Image not found in R2', ['path' => $oldPath]);
            return null;
        }

        // Download image
        $imageContent = $this->defaultStorage->read($oldPath);
        $oldFilename = basename($oldPath);
        $tempFile = $tempDir . '/' . uniqid() . '_' . $oldFilename;

        file_put_contents($tempFile, $imageContent);

        try {
            // Optimize to WebP
            $result = $this->imageOptimizer->optimize($tempFile);

            // Generate new filename
            $newFilename = pathinfo($oldFilename, PATHINFO_FILENAME) . '.webp';
            $newPath = dirname($oldPath) . '/' . $newFilename;

            if (!$dryRun) {
                // Upload optimized image
                $this->defaultStorage->write($newPath, $result->imageData, [
                    'ContentType' => 'image/webp',
                    'CacheControl' => 'public, max-age=31536000',
                ]);

                // Delete old image
                $this->defaultStorage->delete($oldPath);
            }

            $this->logger->info('Image migrated to WebP', [
                'oldPath' => $oldPath,
                'newPath' => $newPath,
                'originalSizeKb' => $result->originalSizeKb,
                'optimizedSizeKb' => $result->optimizedSizeKb,
            ]);

            return $newFilename;
        } finally {
            if ($filesystem->exists($tempFile)) {
                $filesystem->remove($tempFile);
            }
        }
    }
}
