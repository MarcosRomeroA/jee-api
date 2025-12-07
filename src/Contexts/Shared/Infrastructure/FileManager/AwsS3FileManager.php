<?php declare(strict_types=1);

namespace App\Contexts\Shared\Infrastructure\FileManager;

use App\Contexts\Shared\Domain\Exception\UnableToReadFileException;
use App\Contexts\Shared\Domain\FileManager\FileManager;
use Exception;
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
     * @throws Exception
     */
    public function upload(string $tempPath, string $context, string $filename, $checksum = null): void
    {
        $localPath = $tempPath . '/' . $filename;

        if (!is_file($localPath)) {
            throw new Exception("La ruta proporcionada no es un archivo: $localPath");
        }

        $tempStream = fopen('php://temp', 'w+');
        $sourceStream = fopen($localPath, 'r');
        stream_copy_to_stream($sourceStream, $tempStream);
        rewind($tempStream);

        $config = [];
        if ($checksum) {
            $config['ContentMD5'] = $checksum;
        }

        $this->defaultStorage->writeStream(
            $context . '/' . $filename,
            $tempStream,
            $config
        );

        fclose($sourceStream);
        fclose($tempStream);

        if (!unlink($localPath)) {
            throw new Exception("No se pudo eliminar el archivo: $localPath");
        }
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
