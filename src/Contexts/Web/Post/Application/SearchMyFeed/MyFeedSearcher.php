<?php declare(strict_types=1);

namespace App\Contexts\Web\Post\Application\SearchMyFeed;

use App\Contexts\Shared\Domain\FileManager\FileManager;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Post\Application\Shared\GetPostResources;
use App\Contexts\Web\Post\Application\Shared\PostCollectionResponse;
use App\Contexts\Web\Post\Domain\PostRepository;
use Exception;

final readonly class MyFeedSearcher
{
    public function __construct(
        private PostRepository $repository,
        private GetPostResources $getPostResources,
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

        foreach ($posts as $post) {
            $post->setResourceUrls($this->getPostResources->__invoke($post));

            if (!empty($post->getUser()->getProfileImage()->value())) {
                $post->getUser()->setUrlProfileImage(
                    $this->fileManager->generateTemporaryUrl('user/profile', $post->getUser()->getProfileImage()->value())
                );
            }

            $sharedPost = null;
            if ($post->getSharedPostId()){
                $sharedPost = $this->repository->findById($post->getSharedPostId());
                $sharedPost->setResourceUrls($this->getPostResources->__invoke($post));
                $post->setSharedPost($sharedPost);
            }
        }

        return new PostCollectionResponse($posts);
    }
}