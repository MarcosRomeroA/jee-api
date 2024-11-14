<?php declare(strict_types=1);

namespace App\Contexts\Web\Post\Application\Shared;

use App\Contexts\Shared\Domain\FileManager\FileManager;
use App\Contexts\Web\Post\Domain\Post;
use App\Contexts\Web\Post\Domain\PostResource;
use Exception;

final readonly class GetPostResources
{
    public function __construct(
        private FileManager $fileManager
    )
    {
    }

    /**
     * @throws Exception
     */
    public function __invoke(Post $post): array
    {
        $resources = [];

        foreach ($post->getResources() as $postResource) {
            $resources[] = [
                'id' => $postResource->getId()->value(),
                'type' => PostResource::getResourceTypeFromId($postResource->getResourceType()),
                'url' => $this->fileManager->generateTemporaryUrl
                ('posts/'.$post->getId().'/'.PostResource::getResourceTypeFromId(
                        $postResource->getResourceType()
                    ),
                    $postResource->getFilename()
                ),
            ];
        }

        return $resources;
    }
}