<?php declare(strict_types=1);

namespace App\Contexts\Shared\Infrastructure\FileManager;

use App\Contexts\Shared\Domain\Exception\UnableToReadFileException;
use App\Contexts\Shared\Domain\FileManager\FileManager;
use League\Flysystem\FilesystemException;
use League\Flysystem\FilesystemOperator;

final readonly class AwsS3FileManager implements FileManager
{
    public function __construct(private FilesystemOperator $defaultStorage)
    {
    }

    /**
     * @throws FilesystemException
     */
    public function upload(string $tempPath, string $context, string $filename): void
    {
        $path = $context . '/' . $filename;
        $stream = fopen($tempPath, 'r');

        $this->defaultStorage->writeStream(
            $path,
            $stream
        );

        if (is_resource($stream))
            fclose($stream);
    }


    public function download(string $context, string $filename): string{
        $path = $context . '/' . $filename;

        try {
            return $this->defaultStorage->read($path);
        }
        catch (FilesystemException){
            throw new UnableToReadFileException();
        }
    }
}