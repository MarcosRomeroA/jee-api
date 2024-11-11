<?php declare(strict_types=1);

namespace App\Contexts\Shared\Infrastructure\FileManager;

use App\Contexts\Shared\Domain\Exception\UnableToReadFileException;
use App\Contexts\Shared\Domain\FileManager\FileManager;
use GuzzleHttp\Psr7\CachingStream;
use GuzzleHttp\Psr7\Utils;
use League\Flysystem\FilesystemException;
use League\Flysystem\FilesystemOperator;

final readonly class AwsS3FileManager implements FileManager
{
    public function __construct(
        private FilesystemOperator $defaultStorage,
    )
    {
    }

    /**
     * @throws FilesystemException
     */
    public function upload(string $tempPath, string $context, string $filename, $checksum = null): void
    {
        $localPath = $tempPath . '/' . $filename;

        if (!is_file($localPath)) {
            throw new \Exception("La ruta proporcionada no es un archivo: $localPath");
        }

        $tempStream = fopen('php://temp', 'w+');
        $sourceStream = fopen($localPath, 'r');
        stream_copy_to_stream($sourceStream, $tempStream);
        rewind($tempStream); // Rebobina el flujo temporal

        $config = [];
        if ($checksum) {
            $config['ContentMD5'] = $checksum;
        }

        // Sube el archivo a S3 usando la ruta en S3 ($path) y el flujo temporal
        $this->defaultStorage->writeStream(
            $context . '/' . $filename,
            $tempStream,
            $config
        );

        // Cierra los recursos abiertos
        fclose($sourceStream);
        fclose($tempStream);
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

    public function generateTemporaryUrl(string $context, string $filename): string
    {
        $path = $context . '/' . $filename;

        $expiresInSeconds = $_ENV['AWS_EXPIRE_DURATION'];

        return $this->defaultStorage->temporaryUrl($path, (new \DateTime())->modify("+$expiresInSeconds seconds"));
    }
}