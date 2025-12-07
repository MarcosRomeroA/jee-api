<?php declare(strict_types=1);

namespace App\Contexts\Web\Post\Application\Shared;

use App\Contexts\Web\Post\Domain\Post;
use App\Contexts\Web\Post\Domain\PostResource;
use Exception;

final readonly class GetPostResources
{
    public function __construct(
        private string $cdnBaseUrl,
    ) {
    }

    /**
     * @throws Exception
     */
    public function __invoke(Post $post): array
    {
        $resources = [];
        $postId = $post->getId()->value();

        foreach ($post->getResources() as $postResource) {
            $resources[] = [
                'id' => $postResource->getId()->value(),
                'type' => PostResource::getResourceTypeFromId($postResource->getResourceType()),
                'url' => $postResource->getImageUrl($this->cdnBaseUrl, $postId),
            ];
        }

        return $resources;
    }
}
