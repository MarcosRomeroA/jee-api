<?php

declare(strict_types=1);

namespace App\Contexts\Web\Post\Application\AddPostTempResource;

use App\Contexts\Shared\Domain\Bus\Command\Command;
use Symfony\Component\HttpFoundation\File\UploadedFile;

final readonly class AddPostTempResourceCommand implements Command
{
    public function __construct(
        public string $resourceId,
        public string $postId,
        public string $type,
        public UploadedFile $file,
        public string $projectDir
    ) {
    }
}
