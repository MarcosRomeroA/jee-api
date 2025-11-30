<?php

declare(strict_types=1);

namespace App\Contexts\Web\Post\Application\Search;

use App\Contexts\Shared\Domain\CQRS\Query\QueryHandler;
use App\Contexts\Shared\Domain\FileManager\FileManager;
use App\Contexts\Web\Post\Application\Shared\GetPostResources;
use App\Contexts\Web\Post\Application\Shared\PostCollectionResponse;
use App\Contexts\Web\Post\Domain\PostRepository;
use Exception;

final readonly class PostSearcher implements QueryHandler
{
    public function __construct(
        private PostRepository $repository,
        private GetPostResources $getPostResources,
        private FileManager $fileManager,
    ) {
    }

    /**
     * @throws Exception
     */
    public function __invoke(?array $criteria, ?string $currentUserId = null): PostCollectionResponse
    {
        // Merge default pagination values
        $criteriaWithDefaults = array_merge(
            ["limit" => 0, "offset" => 0],
            $criteria ?? [],
        );

        $posts = $criteria
            ? $this->repository->searchByCriteria($criteria)
            : $this->repository->searchAll();

        foreach ($posts as $post) {
            $post->setResourceUrls($this->getPostResources->__invoke($post));

            if (!empty($post->getUser()->getProfileImage()->value())) {
                $post
                    ->getUser()
                    ->setUrlProfileImage(
                        $this->fileManager->generateTemporaryUrl(
                            "user/profile",
                            $post->getUser()->getProfileImage()->value(),
                        ),
                    );
            }

            if ($post->getSharedPostId()) {
                $sharedPost = $this->repository->findById(
                    $post->getSharedPostId(),
                );
                $sharedPost->setResourceUrls(
                    $this->getPostResources->__invoke($sharedPost),
                );

                if (!empty($sharedPost->getUser()->getProfileImage()->value())) {
                    $sharedPost->getUser()->setUrlProfileImage(
                        $this->fileManager->generateTemporaryUrl(
                            'user/profile',
                            $sharedPost->getUser()->getProfileImage()->value(),
                        ),
                    );
                }

                $post->setSharedPost($sharedPost);
            }

            $sharesQuantity = $this->repository->findSharesQuantity(
                $post->getId(),
            );
            $post->setSharesQuantity($sharesQuantity);

            if ($currentUserId !== null) {
                $hasShared = $this->repository->hasUserSharedPost(
                    $post->getId(),
                    new \App\Contexts\Shared\Domain\ValueObject\Uuid($currentUserId),
                );
                $post->setHasShared($hasShared);
            }
        }

        $total = $this->repository->countByCriteria($criteriaWithDefaults);

        return new PostCollectionResponse(
            $posts,
            $criteriaWithDefaults,
            $total,
            $currentUserId,
        );
    }
}
