<?php declare(strict_types=1);

namespace App\Contexts\Web\Post\Application\SearchMyFeed;

use App\Contexts\Shared\Domain\FileManager\FileManager;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Post\Application\Shared\PostCollectionResponse;
use App\Contexts\Web\Post\Domain\PostRepository;
use App\Contexts\Web\Post\Domain\PostResource;
use Exception;

final readonly class MyFeedSearcher
{
    public function __construct(
        private PostRepository $repository,
        private FileManager $fileManager,
    )
    {
    }

    /**
     * @throws Exception
     */
    public function __invoke(Uuid $userId): PostCollectionResponse
    {
        $posts = $this->repository->searchFeed($userId);

        $resources = [];

        foreach ($posts as $post) {
            foreach ($post->getResources() as $postResource) {
                $resources[] = [
                    'id' => $postResource->getId()->value(),
                    'type' => PostResource::getResourceTypeFromId($postResource->getResourceType()),
                    'url' => $this->fileManager->generateTemporaryUrl
                    ('post/'.$post->getId().'/'.PostResource::getResourceTypeFromId(
                            $postResource->getResourceType()
                        ),
                        $postResource->getFilename()
                    ),
                ];
            }

            $post->setResourceUrls($resources);
        }

        return new PostCollectionResponse($posts);
    }
}