<?php declare(strict_types=1);

namespace App\Contexts\Shared\Domain\FileManager;

use GuzzleHttp\Psr7\UploadedFile;

interface FileManager {

    public function upload(string $tempPath, string $context, string $filename): void;

    public function download(string $context, string $filename): string;
}