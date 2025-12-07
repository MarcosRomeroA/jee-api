<?php

declare(strict_types=1);

namespace App\Apps\Console;

use Aws\S3\S3Client;
use League\Flysystem\AwsS3V3\AwsS3V3Adapter;
use League\Flysystem\Filesystem;
use League\Flysystem\StorageAttributes;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:cleanup-old-r2-folder',
    description: 'Removes a folder from R2 bucket',
)]
final class CleanupOldR2FolderCommand extends Command
{
    public function __construct(
        private readonly string $r2Endpoint,
        private readonly string $r2Region,
        private readonly string $r2AccessKey,
        private readonly string $r2SecretKey,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('bucket', InputArgument::REQUIRED, 'The R2 bucket name')
            ->addArgument('prefix', InputArgument::REQUIRED, 'The folder prefix to delete (e.g., "api-jee/")')
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'List files without deleting')
            ->addOption('batch-size', 'b', InputOption::VALUE_REQUIRED, 'Number of files to delete per batch', '100');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $bucket = $input->getArgument('bucket');
        $prefix = $input->getArgument('prefix');
        $dryRun = $input->getOption('dry-run');
        $batchSize = (int) $input->getOption('batch-size');

        $io->title('Cleanup R2 folder');
        $io->info(sprintf('Bucket: %s | Prefix: %s', $bucket, $prefix));

        if ($dryRun) {
            $io->warning('Running in dry-run mode. No files will be deleted.');
        }

        // Create S3 client directly to the base endpoint
        $client = new S3Client([
            'version' => 'latest',
            'region' => $this->r2Region,
            'endpoint' => $this->r2Endpoint,
            'use_path_style_endpoint' => true,
            'credentials' => [
                'key' => $this->r2AccessKey,
                'secret' => $this->r2SecretKey,
            ],
        ]);

        $adapter = new AwsS3V3Adapter($client, $bucket);
        $filesystem = new Filesystem($adapter);

        $io->info('Listing files under prefix: ' . $prefix);

        try {
            $files = $filesystem->listContents($prefix, true)
                ->filter(fn(StorageAttributes $attr) => $attr->isFile())
                ->toArray();

            $totalFiles = count($files);

            if ($totalFiles === 0) {
                $io->success('No files found under ' . self::OLD_PREFIX . '. Nothing to delete.');
                return Command::SUCCESS;
            }

            $io->info(sprintf('Found %d files to delete.', $totalFiles));

            if ($dryRun) {
                $io->section('Files that would be deleted:');
                foreach (array_slice($files, 0, 20) as $file) {
                    $io->writeln('  - ' . $file->path());
                }
                if ($totalFiles > 20) {
                    $io->writeln(sprintf('  ... and %d more files', $totalFiles - 20));
                }
                return Command::SUCCESS;
            }

            if (!$io->confirm(sprintf('Are you sure you want to delete %d files?', $totalFiles), false)) {
                $io->warning('Operation cancelled.');
                return Command::SUCCESS;
            }

            $io->progressStart($totalFiles);

            $deleted = 0;
            $errors = 0;

            foreach ($files as $file) {
                try {
                    $filesystem->delete($file->path());
                    $deleted++;
                } catch (\Throwable $e) {
                    $errors++;
                    $io->error(sprintf('Failed to delete %s: %s', $file->path(), $e->getMessage()));
                }

                $io->progressAdvance();

                if ($deleted % $batchSize === 0) {
                    usleep(100000); // 100ms pause every batch to avoid rate limiting
                }
            }

            $io->progressFinish();

            $io->newLine(2);
            $io->definitionList(
                ['Total files' => $totalFiles],
                ['Deleted' => $deleted],
                ['Errors' => $errors],
            );

            if ($errors > 0) {
                $io->warning('Cleanup completed with errors.');
                return Command::FAILURE;
            }

            $io->success('Cleanup completed successfully.');
            return Command::SUCCESS;

        } catch (\Throwable $e) {
            $io->error('Failed to list files: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
