<?php

declare(strict_types=1);

namespace App\Contexts\Web\Post\Application\AddPostTempResource;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Post\Domain\PostResource;
use Symfony\Component\HttpFoundation\File\UploadedFile;

final readonly class PostTempResourceAdder
{
    public function add(
        string $resourceId,
        string $postId,
        string $type,
        UploadedFile $file,
        string $projectDir
    ): void {
        PostResource::checkIsValidResourceType($type);

        $fileName = (new Uuid($resourceId))->value() . '.' . $file->getClientOriginalExtension();
        $tempFolder = '/var/tmp/resource/' . (new \DateTimeImmutable())->format('Ymd') . '/' . $postId . '/' . $type;
        $uploadDir = $projectDir . $tempFolder;

        $file->move($uploadDir, $fileName);
    }
}
